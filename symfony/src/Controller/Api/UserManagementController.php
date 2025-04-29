<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/users', name: 'api_users_')]
#[IsGranted('ROLE_ADMIN')]
class UserManagementController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function listUsers(EntityManagerInterface $entityManager): JsonResponse
    {
        $users = $entityManager->getRepository(User::class)->findAll();
        
        $userData = [];
        foreach ($users as $user) {
            $userData[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ];
        }
        
        return $this->json([
            'users' => $userData
        ]);
    }
    
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function showUser(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        
        if (!$user) {
            return $this->json([
                'status' => 'error',
                'message' => 'Користувача не знайдено'
            ], 404);
        }
        
        return $this->json([
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ]
        ]);
    }
    
    #[Route('/{id}/roles', name: 'update_roles', methods: ['PATCH'])]
    public function updateRoles(
        int $id, 
        Request $request, 
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $user = $entityManager->getRepository(User::class)->find($id);
        
        if (!$user) {
            return $this->json([
                'status' => 'error',
                'message' => 'Користувача не знайдено'
            ], 404);
        }
        
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['roles']) || !is_array($data['roles'])) {
            return $this->json([
                'status' => 'error',
                'message' => 'Необхідно надати масив ролей'
            ], 400);
        }
        
        // Валідація ролей
        $allowedRoles = ['ROLE_USER', 'ROLE_CLIENT', 'ROLE_MANAGER', 'ROLE_ADMIN'];
        $validRoles = [];
        
        foreach ($data['roles'] as $role) {
            if (in_array($role, $allowedRoles)) {
                $validRoles[] = $role;
            }
        }
        
        $user->setRoles($validRoles);
        $entityManager->flush();
        
        return $this->json([
            'status' => 'success',
            'message' => 'Ролі користувача оновлено',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ]
        ]);
    }
    
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function deleteUser(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        
        if (!$user) {
            return $this->json([
                'status' => 'error',
                'message' => 'Користувача не знайдено'
            ], 404);
        }
        
        // Не дозволяємо видалити самого себе
        if ($user === $this->getUser()) {
            return $this->json([
                'status' => 'error',
                'message' => 'Ви не можете видалити власний обліковий запис'
            ], 400);
        }
        
        $entityManager->remove($user);
        $entityManager->flush();
        
        return $this->json([
            'status' => 'success',
            'message' => 'Користувача успішно видалено'
        ]);
    }
}