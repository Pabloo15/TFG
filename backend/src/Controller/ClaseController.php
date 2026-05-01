<?php

namespace App\Controller;

use App\Entity\Clase;
use App\Form\Clase1Type;
use App\Repository\ClaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/clase')]
final class ClaseController extends AbstractController
{
    #[Route(name: 'app_clase_index', methods: ['GET'])]
    public function index(ClaseRepository $claseRepository): Response
    {
        return $this->render('clase/index.html.twig', [
            'clases' => $claseRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_clase_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $clase = new Clase();
        $form = $this->createForm(Clase1Type::class, $clase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($clase);
            $entityManager->flush();

            return $this->redirectToRoute('app_clase_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('clase/new.html.twig', [
            'clase' => $clase,
            'form' => $form,
        ]);
    }

    /* NUEVO MÉTODO PARA RESERVAR PLAZA */
    #[Route('/{id}/reservar', name: 'app_clase_reservar', methods: ['POST'])]
    public function reservar(Clase $clase, EntityManagerInterface $entityManager): Response
    {
        // Comprobamos si hay plazas libres
        if ($clase->getAforoMaximo() > 0) {
            // Restamos 1 al aforo actual
            $clase->setAforoMaximo($clase->getAforoMaximo() - 1);
            
            // Guardamos el cambio en la base de datos
            $entityManager->flush();

            // Mensaje de éxito
            $this->addFlash('success', '¡Genial! Has reservado tu plaza para ' . $clase->getNombre());
        } else {
            // Mensaje de error si está lleno
            $this->addFlash('danger', 'Lo sentimos, no quedan plazas para esta clase.');
        }

        return $this->redirectToRoute('app_clase_index');
    }

    #[Route('/{id}', name: 'app_clase_show', methods: ['GET'])]
    public function show(Clase $clase): Response
    {
        return $this->render('clase/show.html.twig', [
            'clase' => $clase,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_clase_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Clase $clase, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Clase1Type::class, $clase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_clase_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('clase/edit.html.twig', [
            'clase' => $clase,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_clase_delete', methods: ['POST'])]
    public function delete(Request $request, Clase $clase, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$clase->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($clase);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_clase_index', [], Response::HTTP_SEE_OTHER);
    }
}
