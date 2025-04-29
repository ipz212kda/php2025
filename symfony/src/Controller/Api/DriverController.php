<?php

namespace App\Controller\Api;

use App\Entity\Driver;
use App\Repository\DriverRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/drivers')]
final class DriverController extends AbstractController
{
    #[Route(name: 'api_driver_index', methods: ['GET'])]
    #[IsGranted('ROLE_CLIENT')]
    public function index(Request $request, DriverRepository $driverRepository): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $itemsPerPage = max(1, (int) $request->query->get('itemsPerPage', 10));
        $offset = ($page - 1) * $itemsPerPage;

        $name = $request->query->get('name');
        $phone = $request->query->get('phone');
        $licensePlate = $request->query->get('license_plate');
        $carModel = $request->query->get('car_model');

        $qb = $driverRepository->createQueryBuilder('d');

        if ($name) {
            $qb->andWhere('d.name LIKE :name')->setParameter('name', "%$name%");
        }
        if ($phone) {
            $qb->andWhere('d.phone LIKE :phone')->setParameter('phone', "%$phone%");
        }
        if ($licensePlate) {
            $qb->andWhere('d.license_plate LIKE :plate')->setParameter('plate', "%$licensePlate%");
        }
        if ($carModel) {
            $qb->andWhere('d.car_model LIKE :car')->setParameter('car', "%$carModel%");
        }

        $countQb = clone $qb;
        $countQb->select('COUNT(d.id)');
        $totalItems = (int) $countQb->getQuery()->getSingleScalarResult();

        $qb->setFirstResult($offset)
           ->setMaxResults($itemsPerPage);

        $drivers = $qb->getQuery()->getResult();
        $totalPages = (int) ceil($totalItems / $itemsPerPage);

        $driversData = [];
        foreach ($drivers as $driver) {
            $driversData[] = [
                'id' => $driver->getId(),
                'name' => $driver->getName(),
                'phone' => $driver->getPhone(),
                'license_plate' => $driver->getLicensePlate(),
                'car_model' => $driver->getCarModel(),
            ];
        }

        return $this->json([
            'data' => $driversData,
            'pagination' => [
                'current_page' => $page,
                'items_per_page' => $itemsPerPage,
                'total_items' => $totalItems,
                'total_pages' => $totalPages,
            ]
        ]);
    }

    #[Route('/new', name: 'api_driver_new', methods: ['POST'])]
    #[IsGranted('ROLE_MANAGER')]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['name']) || !isset($data['phone']) || !isset($data['license_plate']) || !isset($data['car_model'])) {
            return $this->json(['status' => 'error', 'message' => 'Відсутні обов\'язкові поля'], Response::HTTP_BAD_REQUEST);
        }
        
        $driver = new Driver();
        $driver->setName($data['name']);
        $driver->setPhone($data['phone']);
        $driver->setLicensePlate($data['license_plate']);
        $driver->setCarModel($data['car_model']);
        
        $entityManager->persist($driver);
        $entityManager->flush();

        return $this->json([
            'status' => 'success',
            'message' => 'Водія успішно створено',
            'data' => [
                'id' => $driver->getId(),
                'name' => $driver->getName(),
                'phone' => $driver->getPhone(),
                'license_plate' => $driver->getLicensePlate(),
                'car_model' => $driver->getCarModel(),
            ]
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_driver_show', methods: ['GET'])]
    #[IsGranted('ROLE_CLIENT')]
    public function show(Driver $driver): JsonResponse
    {
        return $this->json([
            'data' => [
                'id' => $driver->getId(),
                'name' => $driver->getName(),
                'phone' => $driver->getPhone(),
                'license_plate' => $driver->getLicensePlate(),
                'car_model' => $driver->getCarModel(),
            ]
        ]);
    }

    #[Route('/{id}', name: 'api_driver_edit', methods: ['PUT', 'PATCH'])]
    #[IsGranted('ROLE_MANAGER')]
    public function edit(Request $request, Driver $driver, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (isset($data['name'])) {
            $driver->setName($data['name']);
        }
        
        if (isset($data['phone'])) {
            $driver->setPhone($data['phone']);
        }
        
        if (isset($data['license_plate'])) {
            $driver->setLicensePlate($data['license_plate']);
        }
        
        if (isset($data['car_model'])) {
            $driver->setCarModel($data['car_model']);
        }
        
        $entityManager->flush();

        return $this->json([
            'status' => 'success',
            'message' => 'Водія успішно оновлено',
            'data' => [
                'id' => $driver->getId(),
                'name' => $driver->getName(),
                'phone' => $driver->getPhone(),
                'license_plate' => $driver->getLicensePlate(),
                'car_model' => $driver->getCarModel(),
            ]
        ]);
    }

    #[Route('/{id}', name: 'api_driver_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Driver $driver, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($driver);
        $entityManager->flush();

        return $this->json([
            'status' => 'success',
            'message' => 'Водія успішно видалено'
        ]);
    }
}