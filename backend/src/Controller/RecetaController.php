<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RecetaController extends AbstractController
{
    #[Route('/nutricion', name: 'app_recetas', methods: ['GET'])]
    public function index(): Response
    {
        // Abre la vista de las recetas saludables
        return $this->render('nutricion/recetas.html.twig');
    }
}