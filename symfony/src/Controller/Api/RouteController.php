<?php

namespace App\Controller\Api;

use App\Entity\Route;
use App\Repository\RouteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route as RouteAttribute;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[RouteAttribute('/api/routes')]
final class RouteController extends AbstractController
{
    #[RouteAttribute(name: 'api_route_index', methods: ['GET'])]
    #[IsGranted('ROLE_CLIENT')]
    public function index(Request $request, RouteRepository $routeRepository, SerializerInterface $serializer): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $itemsPerPage = max(1, (int) $request->query->get('itemsPerPage', 10));
        $offset = ($page - 1) * $itemsPerPage;

        $start = $request->query->get('start_location');
        $end = $request->query->get('end_location');
        $distance = $request->query->get('distance_km');

        $qb = $routeRepository->createQueryBuilder('r');

        if ($start) {
            $qb->andWhere('r.start_location LIKE :start')->setParameter('start', "%$start%");
        }
        if ($end) {
            $qb->andWhere('r.end_location LIKE :end')->setParameter('end', "%$end%");
        }
        if ($distance !== null && is_numeric($distance)) {
            $qb->andWhere('r.distance_km <= :distance')->setParameter('distance', $distance);
        }

        $countQb = clone $qb;
        $countQb->select('COUNT(r.id)');
        $totalItems = (int) $countQb->getQuery()->getSingleScalarResult();

        $qb->setFirstResult($offset)
           ->setMaxResults($itemsPerPage);

        $routes = $qb->getQuery()->getResult();
        $totalPages = (int) ceil($totalItems / $itemsPerPage);

        $routesData = [];
        foreach ($routes as $route) {
            $routesData[] = [
                'id' => $route->getId(),
                'start_location' => $route->getStartLocation(),
                'end_location' => $route->getEndLocation(),
                'distance_km' => $route->getDistanceKm(),
            ];
        }

        return $this->json([
            'data' => $routesData,
            'pagination' => [
                'current_page' => $page,
                'items_per_page' => $itemsPerPage,
                'total_items' => $totalItems,
                'total_pages' => $totalPages,
            ]
        ]);
    }

    #[RouteAttribute('/new', name: 'api_route_new', methods: ['POST'])]
    #[IsGranted('ROLE_MANAGER')]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['start_location']) || !isset($data['end_location']) || !isset($data['distance_km'])) {
            return $this->json(['status' => 'error', 'message' => 'Відсутні обов\'язкові поля'], Response::HTTP_BAD_REQUEST);
        }
        
        $route = new Route();
        $route->setStartLocation($data['start_location']);
        $route->setEndLocation($data['end_location']);
        $route->setDistanceKm($data['distance_km']);
        
        $entityManager->persist($route);
        $entityManager->flush();

        return $this->json([
            'status' => 'success',
            'message' => 'Маршрут успішно створено',
            'data' => [
                'id' => $route->getId(),
                'start_location' => $route->getStartLocation(),
                'end_location' => $route->getEndLocation(),
                'distance_km' => $route->getDistanceKm(),
            ]
        ], Response::HTTP_CREATED);
    }

    #[RouteAttribute('/{id}', name: 'api_route_show', methods: ['GET'])]
    #[IsGranted('ROLE_CLIENT')]
    public function show(Route $route): JsonResponse
    {
        return $this->json([
            'data' => [
                'id' => $route->getId(),
                'start_location' => $route->getStartLocation(),
                'end_location' => $route->getEndLocation(),
                'distance_km' => $route->getDistanceKm(),
            ]
        ]);
    }

    #[RouteAttribute('/{id}', name: 'api_route_edit', methods: ['PUT', 'PATCH'])]
    #[IsGranted('ROLE_MANAGER')]
    public function edit(Request $request, Route $route, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (isset($data['start_location'])) {
            $route->setStartLocation($data['start_location']);
        }
        
        if (isset($data['end_location'])) {
            $route->setEndLocation($data['end_location']);
        }
        
        if (isset($data['distance_km'])) {
            $route->setDistanceKm($data['distance_km']);
        }
        
        $entityManager->flush();

        return $this->json([
            'status' => 'success',
            'message' => 'Маршрут успішно оновлено',
            'data' => [
                'id' => $route->getId(),
                'start_location' => $route->getStartLocation(),
                'end_location' => $route->getEndLocation(),
                'distance_km' => $route->getDistanceKm(),
            ]
        ]);
    }

    #[RouteAttribute('/{id}', name: 'api_route_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Route $route, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($route);
        $entityManager->flush();

        return $this->json([
            'status' => 'success',
            'message' => 'Маршрут успішно видалено'
        ]);
    }
}