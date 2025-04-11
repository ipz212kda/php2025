<?php

namespace App\Controller;

use App\Entity\Driver;
use App\Form\DriverType;
use App\Repository\DriverRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/driver')]
final class DriverController extends AbstractController
{
    #[Route(name: 'app_driver_index', methods: ['GET'])]
    public function index(Request $request, DriverRepository $driverRepository): Response
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

    return $this->render('driver/index.html.twig', [
        'drivers' => $drivers,
        'currentPage' => $page,
        'itemsPerPage' => $itemsPerPage,
        'totalItems' => $totalItems,
        'totalPages' => $totalPages,
    ]);
}

    #[Route('/new', name: 'app_driver_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $driver = new Driver();
        $form = $this->createForm(DriverType::class, $driver);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($driver);
            $entityManager->flush();

            return $this->redirectToRoute('app_driver_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('driver/new.html.twig', [
            'driver' => $driver,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_driver_show', methods: ['GET'])]
    public function show(Driver $driver): Response
    {
        return $this->render('driver/show.html.twig', [
            'driver' => $driver,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_driver_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Driver $driver, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DriverType::class, $driver);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_driver_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('driver/edit.html.twig', [
            'driver' => $driver,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_driver_delete', methods: ['POST'])]
    public function delete(Request $request, Driver $driver, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$driver->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($driver);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_driver_index', [], Response::HTTP_SEE_OTHER);
    }
}
