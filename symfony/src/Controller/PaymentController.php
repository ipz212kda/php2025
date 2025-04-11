<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Form\PaymentType;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/payment')]
final class PaymentController extends AbstractController
{
    #[Route(name: 'app_payment_index', methods: ['GET'])]
    public function index(Request $request, PaymentRepository $paymentRepository): Response
{
    $page = max(1, (int) $request->query->get('page', 1));
    $itemsPerPage = max(1, (int) $request->query->get('itemsPerPage', 10));
    $offset = ($page - 1) * $itemsPerPage;

    $order = $request->query->get('ride_order_id');
    $method = $request->query->get('payment_method');
    $amount = $request->query->get('amount');
    $paidAt = $request->query->get('paid_at');

    $qb = $paymentRepository->createQueryBuilder('p');

    if (is_numeric($order)) {
        $qb->andWhere('p.rideOrder = :order')->setParameter('order', $order);
    }
    if ($method) {
        $qb->andWhere('p.payment_method LIKE :method')->setParameter('method', "%$method%");
    }
    if (is_numeric($amount)) {
        $qb->andWhere('p.amount = :amount')->setParameter('amount', $amount);
    }
    if ($paidAt) {
        $qb->andWhere('DATE(p.paid_at) = :paidAt')->setParameter('paidAt', new \DateTime($paidAt));
    }

    $countQb = clone $qb;
    $countQb->select('COUNT(p.id)');
    $totalItems = (int) $countQb->getQuery()->getSingleScalarResult();

    $qb->setFirstResult($offset)
       ->setMaxResults($itemsPerPage);

    $payments = $qb->getQuery()->getResult();
    $totalPages = (int) ceil($totalItems / $itemsPerPage);

    return $this->render('payment/index.html.twig', [
        'payments' => $payments,
        'currentPage' => $page,
        'itemsPerPage' => $itemsPerPage,
        'totalItems' => $totalItems,
        'totalPages' => $totalPages,
    ]);
}

    #[Route('/new', name: 'app_payment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $payment = new Payment();
        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($payment);
            $entityManager->flush();

            return $this->redirectToRoute('app_payment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('payment/new.html.twig', [
            'payment' => $payment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_payment_show', methods: ['GET'])]
    public function show(Payment $payment): Response
    {
        return $this->render('payment/show.html.twig', [
            'payment' => $payment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_payment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Payment $payment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_payment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('payment/edit.html.twig', [
            'payment' => $payment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_payment_delete', methods: ['POST'])]
    public function delete(Request $request, Payment $payment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$payment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($payment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_payment_index', [], Response::HTTP_SEE_OTHER);
    }
}
