<?php

namespace App\Controller;

use App\Entity\Receta;
use App\Form\RecetaType;
use App\Repository\RecetaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class RecetaController extends AbstractController
{
    // MUESTRA TODAS LAS RECETAS (Accesible por Socios y Admin)
    #[Route('/nutricion', name: 'app_recetas', methods: ['GET'])]
    public function index(RecetaRepository $recetaRepository): Response
    {
        return $this->render('nutricion/recetas.html.twig', [
            // Pasamos las recetas reales de la base de datos a la plantilla Twig
            'recetas' => $recetaRepository->findAll(),
        ]);
    }

    // FORMULARIO NUEVA RECETA (Solo accesible por el Administrador)
    #[Route('/nutricion/admin/new', name: 'app_receta_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $receta = new Receta();
        $form = $this->createForm(RecetaType::class, $receta);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($receta);
            $entityManager->flush();

            return $this->redirectToRoute('app_recetas', [], Response::HTTP_SEE_OTHER);
        }

        // CORREGIDO: Cambiado 'recetas/new.html.twig' por 'nutricion/new.html.twig'
        return $this->render('nutricion/new.html.twig', [
            'receta' => $receta,
            'form' => $form,
        ]);
    }

    // EDITAR RECETA (Solo accesible por el Administrador)
    #[Route('/nutricion/admin/{id}/edit', name: 'app_receta_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Receta $receta, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecetaType::class, $receta);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_recetas', [], Response::HTTP_SEE_OTHER);
        }

        // CORREGIDO: Cambiado 'recetas/edit.html.twig' por 'nutricion/edit.html.twig'
        return $this->render('nutricion/edit.html.twig', [
            'receta' => $receta,
            'form' => $form,
        ]);
    }

    // ELIMINAR RECETA (Solo accesible por el Administrador)
    #[Route('/nutricion/admin/{id}', name: 'app_receta_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Receta $receta, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$receta->getId(), $request->request->get('_token'))) {
            $entityManager->remove($receta);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_recetas', [], Response::HTTP_SEE_OTHER);
    }
}