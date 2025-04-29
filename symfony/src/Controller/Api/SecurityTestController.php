<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api', name: 'api_')]
class SecurityTestController extends AbstractController
{
    #[Route('/public', name: 'public', methods: ['GET'])]
    public function publicEndpoint(): JsonResponse
    {
        return $this->json([
            'message' => 'Це публічний API ендпоінт, доступний всім'
        ]);
    }

    #[Route('/profile', name: 'profile', methods: ['GET'])]
    public function userProfile(): JsonResponse
    {
        $user = $this->getUser();
        
        return $this->json([
            'message' => 'Вітаємо в особистому кабінеті',
            'user' => [
                'email' => $user->getUserIdentifier(),
                'roles' => $user->getRoles()
            ]
        ]);
    }

    #[Route('/client', name: 'client_only', methods: ['GET'])]
    #[IsGranted('ROLE_CLIENT')]
    public function clientData(): JsonResponse
    {
        return $this->json([
            'message' => 'Ця інформація доступна тільки клієнтам',
            'data' => [
                'clientInfo' => '123'
            ]
        ]);
    }

    #[Route('/manager', name: 'manager_only', methods: ['GET'])]
    #[IsGranted('ROLE_MANAGER')]
    public function managerData(): JsonResponse
    {
        return $this->json([
            'message' => 'Ця інформація доступна тільки менеджерам',
            'data' => [
                'managerInfo' => '123'
            ]
        ]);
    }

    #[Route('/admin', name: 'admin_only', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminData(): JsonResponse
    {
        return $this->json([
            'message' => 'Ця інформація доступна тільки адміністраторам',
            'data' => [
                'adminInfo' => '123'
            ]
        ]);
    }
}