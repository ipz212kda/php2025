<?php

namespace App\Controller;

use App\Entity\Route;
use App\Form\RouteType;
use App\Repository\RouteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route as RouteAttribute;

#[RouteAttribute('/route')]
final class RouteController extends AbstractController
{
    #[RouteAttribute(name: 'app_route_index', methods: ['GET'])]
    public function index(RouteRepository $routeRepository): Response
    {
        return $this->render('route/index.html.twig', [
            'routes' => $routeRepository->findAll(),
        ]);
    }

    #[RouteAttribute('/new', name: 'app_route_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $route = new Route();
        $form = $this->createForm(RouteType::class, $route);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($route);
            $entityManager->flush();

            return $this->redirectToRoute('app_route_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('route/new.html.twig', [
            'route' => $route,
            'form' => $form,
        ]);
    }

    #[RouteAttribute('/{id}', name: 'app_route_show', methods: ['GET'])]
    public function show(Route $route): Response
    {
        return $this->render('route/show.html.twig', [
            'route' => $route,
        ]);
    }

    #[RouteAttribute('/{id}/edit', name: 'app_route_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Route $route, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RouteType::class, $route);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_route_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('route/edit.html.twig', [
            'route' => $route,
            'form' => $form,
        ]);
    }

    #[RouteAttribute('/{id}', name: 'app_route_delete', methods: ['POST'])]
    public function delete(Request $request, Route $route, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$route->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($route);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_route_index', [], Response::HTTP_SEE_OTHER);
    }
}
