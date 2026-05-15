<?php

namespace App\Controller;

use App\Entity\Rutina;
use App\Repository\RutinaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/rutinas')]
class RutinaController extends AbstractController
{
    /**
     * Lista todas las rutinas
     */
    #[Route('', name: 'app_rutina_index', methods: ['GET'])]
    public function index(RutinaRepository $rutinaRepository): Response
    {
        return $this->render('rutina/index.html.twig', [
            'rutinas' => $rutinaRepository->findAll(),
        ]);
    }

    /**
     * Crea una nueva rutina
     */
    #[Route('/nueva', name: 'app_rutina_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $rutina = new Rutina();
            $rutina->setNombre($request->request->get('nombre'));
            $rutina->setEjercicios($request->request->get('ejercicios'));

            $em->persist($rutina);
            $em->flush();

            $this->addFlash('success', 'Rutina creada correctamente.');
            return $this->redirectToRoute('app_rutina_index');
        }

        return $this->render('rutina/new.html.twig');
    }

    /**
     * Edita una rutina existente
     */
    #[Route('/{id}/editar', name: 'app_rutina_edit', methods: ['GET', 'POST'])]
    public function edit(Rutina $rutina, Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $rutina->setNombre($request->request->get('nombre'));
            $rutina->setEjercicios($request->request->get('ejercicios'));

            $em->flush();

            $this->addFlash('success', 'Rutina actualizada correctamente.');
            return $this->redirectToRoute('app_rutina_index');
        }

        return $this->render('rutina/edit.html.twig', [
            'rutina' => $rutina,
        ]);
    }

    /**
     * Elimina una rutina
     */
    #[Route('/{id}/eliminar', name: 'app_rutina_delete', methods: ['POST'])]
    public function delete(Request $request, Rutina $rutina, EntityManagerInterface $em): Response
    {
        // Verificamos el token CSRF para que nadie borre rutinas por error
        if ($this->isCsrfTokenValid('delete'.$rutina->getId(), $request->request->get('_token'))) {
            $em->remove($rutina);
            $em->flush();
            $this->addFlash('success', 'Rutina eliminada.');
        }

        return $this->redirectToRoute('app_rutina_index');
    }
}