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
    public function index(ClaseRepository $claseRepository, Request $request): Response
    {
        $session = $request->getSession();
        
        // Ahora recuperamos un mapa de reservas de la sesión. 
        // El formato será: ['ID_CLASE-YYYY-MM-DD' => 'YYYY-MM-DD', ...]
        $misReservas = $session->get('mis_reservas_completas', []);

        return $this->render('clase/index.html.twig', [
            'clases' => $claseRepository->findAll(),
            'mis_reservas' => $misReservas, // Enviamos el mapa completo a Twig
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

    #[Route('/{id}/reservar', name: 'app_clase_reservar', methods: ['POST'])]
    public function reservar(Clase $clase, Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        $misReservas = $session->get('mis_reservas_completas', []);

        // Capturamos de forma segura la fecha del día que se pulsó en el calendario flotante
        $fechaReserva = $request->request->get('fecha_reserva'); 

        if (!$fechaReserva) {
            $this->addFlash('danger', 'No se ha seleccionado una fecha válida.');
            return $this->redirectToRoute('app_clase_index');
        }

        // Creamos un identificador único para esta clase EN ESTE DÍA específico
        $claveReserva = $clase->getId() . '_' . $fechaReserva;

        // Validamos si ya está reservada para ese día concreto
        if (array_key_exists($claveReserva, $misReservas)) {
            $this->addFlash('warning', 'Ya tienes una reserva para esta clase en la fecha ' . $fechaReserva);
            return $this->redirectToRoute('app_clase_index');
        }

        // Comprobamos si hay plazas libres
        if ($clase->getAforoMaximo() > 0) {
            // Restamos 1 plaza de la base de datos
            $clase->setAforoMaximo($clase->getAforoMaximo() - 1);
            $entityManager->flush();

            // Guardamos la reserva vinculando de verdad la Clase con la Fecha elegida
            $misReservas[$claveReserva] = [
                'clase_id' => $clase->getId(),
                'fecha' => $fechaReserva
            ];
            $session->set('mis_reservas_completas', $misReservas);

            // Convertimos la fecha a un formato más amigable para el mensaje de éxito (ej: 21/05/2026)
            $fechaFormateada = date("d/m/Y", strtotime($fechaReserva));
            $this->addFlash('success', '¡Plaza reservada para ' . $clase->getNombre() . ' el día ' . $fechaFormateada . '!');
        } else {
            $this->addFlash('danger', 'Lo sentimos, no quedan plazas libres.');
        }

        return $this->redirectToRoute('app_clase_index');
    }

    #[Route('/{id}/cancelar', name: 'app_clase_cancelar', methods: ['POST'])]
    public function cancelar(Clase $clase, Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        $misReservas = $session->get('mis_reservas_completas', []);
        
        // Capturamos qué fecha exacta se quiere cancelar
        $fechaCancelar = $request->request->get('fecha_reserva_cancelar');
        $claveReserva = $clase->getId() . '_' . $fechaCancelar;

        if (array_key_exists($claveReserva, $misReservas)) {
            // Devolvemos la plaza sumando +1
            $clase->setAforoMaximo($clase->getAforoMaximo() + 1);
            $entityManager->flush();

            // Eliminamos la reserva específica de ese día
            unset($misReservas[$claveReserva]);
            $session->set('mis_reservas_completas', $misReservas);

            $this->addFlash('success', 'Reserva del día ' . date("d/m/Y", strtotime($fechaCancelar)) . ' cancelada. Plaza liberada.');
        } else {
            $this->addFlash('danger', 'No se ha podido encontrar la reserva para esta fecha.');
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