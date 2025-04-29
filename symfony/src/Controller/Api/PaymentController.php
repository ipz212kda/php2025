<?php

namespace App\Controller\Api;

use App\Entity\Payment;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/payments')]
final class PaymentController extends AbstractController
{
    #[Route(name: 'api_payment_index', methods: ['GET'])]
    #[IsGranted('ROLE_CLIENT')]
    public function index(Request $request, PaymentRepository $paymentRepository): JsonResponse
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

        $paymentsData = [];
        foreach ($payments as $payment) {
            $paymentsData[] = [
                'id' => $payment->getId(),
                'ride_order_id' => $payment->getRideOrder() ? $payment->getRideOrder()->getId() : null,
                'payment_method' => $payment->getPaymentMethod(),
                'amount' => $payment->getAmount(),
                'paid_at' => $payment->getPaidAt() ? $payment->getPaidAt()->format('Y-m-d H:i:s') : null,
            ];
        }

        return $this->json([
            'data' => $paymentsData,
            'pagination' => [
                'current_page' => $page,
                'items_per_page' => $itemsPerPage,
                'total_items' => $totalItems,
                'total_pages' => $totalPages,
            ]
        ]);
    }

    #[Route('/new', name: 'api_payment_new', methods: ['POST'])]
    #[IsGranted('ROLE_MANAGER')]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['ride_order_id']) || !isset($data['payment_method']) || !isset($data['amount'])) {
            return $this->json(['status' => 'error', 'message' => 'Відсутні обов\'язкові поля'], Response::HTTP_BAD_REQUEST);
        }
        
        // Перевірка існування замовлення
        $rideOrder = $entityManager->getRepository(\App\Entity\RideOrder::class)->find($data['ride_order_id']);
        if (!$rideOrder) {
            return $this->json(['status' => 'error', 'message' => 'Замовлення не знайдено'], Response::HTTP_BAD_REQUEST);
        }
        
        $payment = new Payment();
        $payment->setRideOrder($rideOrder);
        $payment->setPaymentMethod($data['payment_method']);
        $payment->setAmount($data['amount']);
        
        if (isset($data['paid_at'])) {
            $payment->setPaidAt(new \DateTime($data['paid_at']));
        } else {
            $payment->setPaidAt(new \DateTime());
        }
        
        $entityManager->persist($payment);
        $entityManager->flush();

        return $this->json([
            'status' => 'success',
            'message' => 'Платіж успішно створено',
            'data' => [
                'id' => $payment->getId(),
                'ride_order_id' => $payment->getRideOrder()->getId(),
                'payment_method' => $payment->getPaymentMethod(),
                'amount' => $payment->getAmount(),
                'paid_at' => $payment->getPaidAt()->format('Y-m-d H:i:s'),
            ]
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_payment_show', methods: ['GET'])]
    #[IsGranted('ROLE_CLIENT')]
    public function show(Payment $payment): JsonResponse
    {
        return $this->json([
            'data' => [
                'id' => $payment->getId(),
                'ride_order_id' => $payment->getRideOrder() ? $payment->getRideOrder()->getId() : null,
                'payment_method' => $payment->getPaymentMethod(),
                'amount' => $payment->getAmount(),
                'paid_at' => $payment->getPaidAt() ? $payment->getPaidAt()->format('Y-m-d H:i:s') : null,
            ]
        ]);
    }

    #[Route('/{id}', name: 'api_payment_edit', methods: ['PUT', 'PATCH'])]
    #[IsGranted('ROLE_MANAGER')]
    public function edit(Request $request, Payment $payment, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (isset($data['ride_order_id'])) {
            $rideOrder = $entityManager->getRepository(\App\Entity\RideOrder::class)->find($data['ride_order_id']);
            if (!$rideOrder) {
                return $this->json(['status' => 'error', 'message' => 'Замовлення не знайдено'], Response::HTTP_BAD_REQUEST);
            }
            $payment->setRideOrder($rideOrder);
        }
        
        if (isset($data['payment_method'])) {
            $payment->setPaymentMethod($data['payment_method']);
        }
        
        if (isset($data['amount'])) {
            $payment->setAmount($data['amount']);
        }
        
        if (isset($data['paid_at'])) {
            $payment->setPaidAt(new \DateTime($data['paid_at']));
        }
        
        $entityManager->flush();

        return $this->json([
            'status' => 'success',
            'message' => 'Платіж успішно оновлено',
            'data' => [
                'id' => $payment->getId(),
                'ride_order_id' => $payment->getRideOrder() ? $payment->getRideOrder()->getId() : null,
                'payment_method' => $payment->getPaymentMethod(),
                'amount' => $payment->getAmount(),
                'paid_at' => $payment->getPaidAt() ? $payment->getPaidAt()->format('Y-m-d H:i:s') : null,
            ]
        ]);
    }

    #[Route('/{id}', name: 'api_payment_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Payment $payment, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($payment);
        $entityManager->flush();

        return $this->json([
            'status' => 'success',
            'message' => 'Платіж успішно видалено'
        ]);
    }
}