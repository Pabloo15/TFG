<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route; // Usamos Attribute en lugar de Annotation

class GimnasioController extends AbstractController
{
    #[Route('/seleccionar-centro', name: 'app_seleccionar_centro')]
    public function seleccionar(): Response
    {
        return $this->render('gimnasio/seleccionar.html.twig');
    }
}
