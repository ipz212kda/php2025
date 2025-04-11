<?php

namespace App\Controller;

use App\Entity\RideOrder;
use App\Form\RideOrderType;
use App\Repository\RideOrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/ride/order')]
final class RideOrderController extends AbstractController
{
    #[Route(name: 'app_ride_order_index', methods: ['GET'])]
    public function index(Request $request, RideOrderRepository $rideOrderRepository): Response
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

    $rideOrder = $qb->getQuery()->getResult();
    $totalPages = ceil($totalItems / $itemsPerPage);

    return $this->render('ride_order/index.html.twig', [
        'rideOrder' => $rideOrder,
        'currentPage' => $page,
        'itemsPerPage' => $itemsPerPage,
        'totalItems' => $totalItems,
        'totalPages' => $totalPages,
    ]);
    }

    #[Route('/new', name: 'app_ride_order_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $rideOrder = new RideOrder();
        $form = $this->createForm(RideOrderType::class, $rideOrder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rideOrder);
            $entityManager->flush();

            return $this->redirectToRoute('app_ride_order_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ride_order/new.html.twig', [
            'ride_order' => $rideOrder,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ride_order_show', methods: ['GET'])]
    public function show(RideOrder $rideOrder): Response
    {
        return $this->render('ride_order/show.html.twig', [
            'ride_order' => $rideOrder,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ride_order_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RideOrder $rideOrder, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RideOrderType::class, $rideOrder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ride_order_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ride_order/edit.html.twig', [
            'ride_order' => $rideOrder,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ride_order_delete', methods: ['POST'])]
    public function delete(Request $request, RideOrder $rideOrder, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rideOrder->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($rideOrder);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ride_order_index', [], Response::HTTP_SEE_OTHER);
    }
}
