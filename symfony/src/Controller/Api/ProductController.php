<?php

namespace App\Controller\Api;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/products')]
final class ProductController extends AbstractController
{
    #[Route(name: 'api_product_index', methods: ['GET'])]
    #[IsGranted('ROLE_CLIENT')]
    public function index(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findAll();
        
        $productsData = [];
        foreach ($products as $product) {
            $productsData[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'category' => $product->getCategory(),
                'price' => $product->getPrice(),
            ];
        }
        
        return $this->json([
            'data' => $productsData
        ]);
    }

    #[Route('/new', name: 'api_product_new', methods: ['POST'])]
    #[IsGranted('ROLE_MANAGER')]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['name']) || !isset($data['price'])) {
            return $this->json(['status' => 'error', 'message' => 'Відсутні обов\'язкові поля'], Response::HTTP_BAD_REQUEST);
        }
        
        $product = new Product();
        $product->setName($data['name']);
        $product->setPrice($data['price']);
        
        if (isset($data['category'])) {
            $product->setCategory($data['category']);
        }
        
        $entityManager->persist($product);
        $entityManager->flush();

        return $this->json([
            'status' => 'success',
            'message' => 'Продукт успішно створено',
            'data' => [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'category' => $product->getCategory(),
                'price' => $product->getPrice(),
            ]
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_product_show', methods: ['GET'])]
    #[IsGranted('ROLE_CLIENT')]
    public function show(Product $product): JsonResponse
    {
        return $this->json([
            'data' => [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'category' => $product->getCategory(),
                'price' => $product->getPrice(),
            ]
        ]);
    }

    #[Route('/{id}', name: 'api_product_edit', methods: ['PUT', 'PATCH'])]
    #[IsGranted('ROLE_MANAGER')]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (isset($data['name'])) {
            $product->setName($data['name']);
        }
        
        if (isset($data['category'])) {
            $product->setCategory($data['category']);
        }
        
        if (isset($data['price'])) {
            $product->setPrice($data['price']);
        }
        
        $entityManager->flush();

        return $this->json([
            'status' => 'success',
            'message' => 'Продукт успішно оновлено',
            'data' => [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'category' => $product->getCategory(),
                'price' => $product->getPrice(),
            ]
        ]);
    }

    #[Route('/{id}', name: 'api_product_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Product $product, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($product);
        $entityManager->flush();

        return $this->json([
            'status' => 'success',
            'message' => 'Продукт успішно видалено'
        ]);
    }
}