<?php

namespace App\Controller;

use App\Entity\Rutina;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RutinaController extends AbstractController
{
    #[Route('/rutinas', name: 'app_rutina_index')]
    public function index(EntityManagerInterface $em): Response
    {
        // Buscamos todas las rutinas guardadas en la base de datos
        $rutinas = $em->getRepository(Rutina::class)->findAll();

        return $this->render('rutina/index.html.twig', [
            'rutinas' => $rutinas,
        ]);
    }

    #[Route('/rutinas/nueva', name: 'app_rutina_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        // Si el usuario envía el formulario
        if ($request->isMethod('POST')) {
            $rutina = new Rutina();
            $rutina->setNombre($request->request->get('nombre'));
            $rutina->setEjercicios($request->request->get('ejercicios'));

            $em->persist($rutina);
            $em->flush();

            // Al terminar, volvemos a la lista
            return $this->redirectToRoute('app_rutina_index');
        }

        return $this->render('rutina/new.html.twig');
    }
}

