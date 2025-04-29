<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/users', name: 'api_admin_users', methods: ['GET'])]
    public function listUsers(EntityManagerInterface $entityManager): JsonResponse
    {
        $users = $entityManager->getRepository(User::class)->findAll();
        
        $usersData = [];
        foreach ($users as $user) {
            $usersData[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ];
        }
        
        return $this->json([
            'data' => $usersData
        ]);
    }
    
    #[Route('/users/{id}', name: 'api_admin_user_show', methods: ['GET'])]
    public function showUser(User $user): JsonResponse
    {
        return $this->json([
            'data' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ]
        ]);
    }
    
    #[Route('/users', name: 'api_admin_user_create', methods: ['POST'])]
    public function createUser(
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['email']) || !isset($data['password'])) {
            return $this->json(['status' => 'error', 'message' => 'Відсутні обов\'язкові поля'], Response::HTTP_BAD_REQUEST);
        }
        
        // Перевірка, чи існує користувач з таким email
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return $this->json(['status' => 'error', 'message' => 'Користувач з таким email вже існує'], Response::HTTP_BAD_REQUEST);
        }
        
        $user = new User();
        $user->setEmail($data['email']);
        
        // Хешування пароля
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);
        
        // Встановлення ролей
        if (isset($data['roles']) && is_array($data['roles'])) {
            $user->setRoles($data['roles']);
        } else {
            $user->setRoles(['ROLE_CLIENT']);
        }
        
        $entityManager->persist($user);
        $entityManager->flush();
        
        return $this->json([
            'status' => 'success',
            'message' => 'Користувача успішно створено',
            'data' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ]
        ], Response::HTTP_CREATED);
    }
    
    #[Route('/users/{id}', name: 'api_admin_user_update', methods: ['PUT', 'PATCH'])]
    public function updateUser(
        User $user,
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        
        // Оновлення email
        if (isset($data['email'])) {
            // Перевірка, чи існує інший користувач з таким email
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
            if ($existingUser && $existingUser->getId() !== $user->getId()) {
                return $this->json(['status' => 'error', 'message' => 'Користувач з таким email вже існує'], Response::HTTP_BAD_REQUEST);
            }
            
            $user->setEmail($data['email']);
        }
        
        // Оновлення паролю
        if (isset($data['password'])) {
            $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);
        }
        
        // Оновлення ролей
        if (isset($data['roles']) && is_array($data['roles'])) {
            $user->setRoles($data['roles']);
        }
        
        $entityManager->flush();
        
        return $this->json([
            'status' => 'success',
            'message' => 'Користувача успішно оновлено',
            'data' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ]
        ]);
    }
    
    #[Route('/users/{id}', name: 'api_admin_user_delete', methods: ['DELETE'])]
    public function deleteUser(User $user, EntityManagerInterface $entityManager): JsonResponse
    {
        // Перевіряємо, чи адміністратор не намагається видалити себе
        if ($user === $this->getUser()) {
            return $this->json(['status' => 'error', 'message' => 'Ви не можете видалити свій власний акаунт'], Response::HTTP_BAD_REQUEST);
        }
        
        $entityManager->remove($user);
        $entityManager->flush();
        
        return $this->json([
            'status' => 'success',
            'message' => 'Користувача успішно видалено'
        ]);
    }
}