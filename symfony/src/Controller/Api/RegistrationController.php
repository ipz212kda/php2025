<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $passwordHasher, 
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['email']) || !isset($data['password'])) {
            return $this->json([
                'status' => 'error',
                'message' => 'Відсутні обов\'язкові поля'
            ], 400);
        }
        
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return $this->json([
                'status' => 'error',
                'message' => 'Користувач з таким email вже існує'
            ], 400);
        }
        
        // Створення нового користувача
        $user = new User();
        $user->setEmail($data['email']);
        
        // Хешування пароля
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);
        
        // Встановлення ролі (за замовчуванням - ROLE_CLIENT)
        // Роль ROLE_CLIENT автоматично додається в методі getRoles() сутності User
        
        // Збереження користувача в базі даних
        $entityManager->persist($user);
        $entityManager->flush();
        
        return $this->json([
            'status' => 'success',
            'message' => 'Користувача успішно зареєстровано'
        ], 201);
    }
}