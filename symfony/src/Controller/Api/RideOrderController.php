<?php

namespace App\Controller\Api;

use App\Entity\RideOrder;
use App\Repository\RideOrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/ride-orders')]
final class RideOrderController extends AbstractController
{
    #[Route(name: 'api_ride_order_index', methods: ['GET'])]
    #[IsGranted('ROLE_CLIENT')]
    public function index(Request $request, RideOrderRepository $rideOrderRepository): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $itemsPerPage = max(1, (int) $request->query->get('itemsPerPage', 10));
        $offset = ($page - 1) * $itemsPerPage;

        $clientId = $request->query->get('client_id');
        $driverId = $request->query->get('driver_id');
        $routeId = $request->query->get('route_id');
        $status = $request->query->get('status');
        $createdAt = $request->query->get('created_at');

        $qb = $rideOrderRepository->createQueryBuilder('r');

        if ($clientId) {
            $qb->andWhere('r.client = :clientId')->setParameter('clientId', $clientId);
        }
        if ($driverId) {
            $qb->andWhere('r.driver = :driverId')->setParameter('driverId', $driverId);
        }
        if ($routeId) {
            $qb->andWhere('r.route = :routeId')->setParameter('routeId', $routeId);
        }
        if ($status) {
            $qb->andWhere('r.status LIKE :status')->setParameter('status', "%$status%");
        }
        if ($createdAt) {
            $qb->andWhere('DATE(r.created_at) = :createdAt')->setParameter('createdAt', new \DateTime($createdAt));
        }

        $countQb = clone $qb;
        $countQb->select('COUNT(r.id)');
        $totalItems = (int) $countQb->getQuery()->getSingleScalarResult();

        $qb->setFirstResult($offset)
           ->setMaxResults($itemsPerPage);

        $rideOrders = $qb->getQuery()->getResult();
        $totalPages = ceil($totalItems / $itemsPerPage);

        $rideOrdersData = [];
        foreach ($rideOrders as $order) {
            $rideOrdersData[] = [
                'id' => $order->getId(),
                'client_id' => $order->getClient() ? $order->getClient()->getId() : null,
                'driver_id' => $order->getDriver() ? $order->getDriver()->getId() : null,
                'route_id' => $order->getRoute() ? $order->getRoute()->getId() : null,
                'status' => $order->getStatus(),
                'created_at' => $order->getCreatedAt() ? $order->getCreatedAt()->format('Y-m-d H:i:s') : null,
            ];
        }

        return $this->json([
            'data' => $rideOrdersData,
            'pagination' => [
                'current_page' => $page,
                'items_per_page' => $itemsPerPage,
                'total_items' => $totalItems,
                'total_pages' => $totalPages,
            ]
        ]);
    }

    #[Route('/new', name: 'api_ride_order_new', methods: ['POST'])]
    #[IsGranted('ROLE_MANAGER')]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['client_id']) || !isset($data['driver_id']) || !isset($data['route_id'])) {
            return $this->json(['status' => 'error', 'message' => 'Відсутні обов\'язкові поля'], Response::HTTP_BAD_REQUEST);
        }
        
        // Перевірка існування клієнта, водія та маршруту
        $client = $entityManager->getRepository(\App\Entity\User::class)->find($data['client_id']);
        $driver = $entityManager->getRepository(\App\Entity\Driver::class)->find($data['driver_id']);
        $route = $entityManager->getRepository(\App\Entity\Route::class)->find($data['route_id']);
        
        if (!$client || !$driver || !$route) {
            return $this->json(['status' => 'error', 'message' => 'Клієнт, водій або маршрут не знайдені'], Response::HTTP_BAD_REQUEST);
        }
        
        $rideOrder = new RideOrder();
        $rideOrder->setClient($client);
        $rideOrder->setDriver($driver);
        $rideOrder->setRoute($route);
        
        if (isset($data['status'])) {
            $rideOrder->setStatus($data['status']);
        } else {
            $rideOrder->setStatus('pending');
        }
        
        $rideOrder->setCreatedAt(new \DateTime());
        
        $entityManager->persist($rideOrder);
        $entityManager->flush();

        return $this->json([
            'status' => 'success',
            'message' => 'Замовлення поїздки успішно створено',
            'data' => [
                'id' => $rideOrder->getId(),
                'client_id' => $rideOrder->getClient()->getId(),
                'driver_id' => $rideOrder->getDriver()->getId(),
                'route_id' => $rideOrder->getRoute()->getId(),
                'status' => $rideOrder->getStatus(),
                'created_at' => $rideOrder->getCreatedAt()->format('Y-m-d H:i:s'),
            ]
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_ride_order_show', methods: ['GET'])]
    #[IsGranted('ROLE_CLIENT')]
    public function show(RideOrder $rideOrder): JsonResponse
    {
        return $this->json([
            'data' => [
                'id' => $rideOrder->getId(),
                'client_id' => $rideOrder->getClient() ? $rideOrder->getClient()->getId() : null,
                'driver_id' => $rideOrder->getDriver() ? $rideOrder->getDriver()->getId() : null,
                'route_id' => $rideOrder->getRoute() ? $rideOrder->getRoute()->getId() : null,
                'status' => $rideOrder->getStatus(),
                'created_at' => $rideOrder->getCreatedAt() ? $rideOrder->getCreatedAt()->format('Y-m-d H:i:s') : null,
            ]
        ]);
    }

    #[Route('/{id}', name: 'api_ride_order_edit', methods: ['PUT', 'PATCH'])]
    #[IsGranted('ROLE_MANAGER')]
    public function edit(Request $request, RideOrder $rideOrder, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (isset($data['client_id'])) {
            $client = $entityManager->getRepository(\App\Entity\User::class)->find($data['client_id']);
            if (!$client) {
                return $this->json(['status' => 'error', 'message' => 'Клієнт не знайдений'], Response::HTTP_BAD_REQUEST);
            }
            $rideOrder->setClient($client);
        }
        
        if (isset($data['driver_id'])) {
            $driver = $entityManager->getRepository(\App\Entity\Driver::class)->find($data['driver_id']);
            if (!$driver) {
                return $this->json(['status' => 'error', 'message' => 'Водій не знайдений'], Response::HTTP_BAD_REQUEST);
            }
            $rideOrder->setDriver($driver);
        }
        
        if (isset($data['route_id'])) {
            $route = $entityManager->getRepository(\App\Entity\Route::class)->find($data['route_id']);
            if (!$route) {
                return $this->json(['status' => 'error', 'message' => 'Маршрут не знайдений'], Response::HTTP_BAD_REQUEST);
            }
            $rideOrder->setRoute($route);
        }
        
        if (isset($data['status'])) {
            $rideOrder->setStatus($data['status']);
        }
        
        $entityManager->flush();

        return $this->json([
            'status' => 'success',
            'message' => 'Замовлення поїздки успішно оновлено',
            'data' => [
                'id' => $rideOrder->getId(),
                'client_id' => $rideOrder->getClient() ? $rideOrder->getClient()->getId() : null,
                'driver_id' => $rideOrder->getDriver() ? $rideOrder->getDriver()->getId() : null,
                'route_id' => $rideOrder->getRoute() ? $rideOrder->getRoute()->getId() : null,
                'status' => $rideOrder->getStatus(),
                'created_at' => $rideOrder->getCreatedAt() ? $rideOrder->getCreatedAt()->format('Y-m-d H:i:s') : null,
            ]
        ]);
    }

    #[Route('/{id}', name: 'api_ride_order_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(RideOrder $rideOrder, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($rideOrder);
        $entityManager->flush();

        return $this->json([
            'status' => 'success',
            'message' => 'Замовлення поїздки успішно видалено'
        ]);
    }
}