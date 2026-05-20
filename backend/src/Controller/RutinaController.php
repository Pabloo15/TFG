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
    #[Route('', name: 'app_rutinas', methods: ['GET'])]
    public function index(RutinaRepository $rutinaRepository): Response
    {
        return $this->render('rutina/index.html.twig', [
            'rutinas' => $rutinaRepository->findAll(),
        ]);
    }

    /**
     * Crea una nueva rutina procesando múltiples ejercicios y series dinámicas
     */
    #[Route('/nueva', name: 'app_rutina_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $rutina = new Rutina();

        if ($request->isMethod('POST')) {
            $rutina->setNombre($request->request->get('nombre'));
            
            // Recogemos el árbol estructurado de ejercicios y series enviados desde Twig
            $ejerciciosInput = $request->request->all('ejercicios') ?? [];
            $textoFinal = "";

            foreach ($ejerciciosInput as $ejer) {
                $nombreEjer = trim($ejer['nombre'] ?? '');
                if (!empty($nombreEjer)) {
                    $textoFinal .= "• {$nombreEjer}\n";
                    $series = $ejer['series'] ?? [];
                    
                    foreach ($series as $index => $serieData) {
                        $reps = !empty($serieData['reps']) ? $serieData['reps'] : '12';
                        $kilos = !empty($serieData['kilos']) ? $serieData['kilos'] : '0';
                        $numSerie = $index + 1;
                        $textoFinal .= "  - Serie {$numSerie}: {$reps} Reps x {$kilos} kg\n";
                    }
                }
            }

            $rutina->setEjercicios(trim($textoFinal));

            $em->persist($rutina);
            $em->flush();

            $this->addFlash('success', 'Rutina creada correctamente.');
            return $this->redirectToRoute('app_rutinas');
        }

        return $this->render('rutina/new.html.twig', [
            'rutina' => $rutina,
        ]);
    }

    /**
     * Edita una rutina desglosando Ejercicios y Series individuales
     */
    #[Route('/{id}/editar', name: 'app_rutina_edit', methods: ['GET', 'POST'])]
    public function edit(Rutina $rutina, Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $rutina->setNombre($request->request->get('nombre'));
            
            $ejerciciosInput = $request->request->all('ejercicios') ?? [];
            $textoFinal = "";

            foreach ($ejerciciosInput as $ejer) {
                $nombreEjer = trim($ejer['nombre'] ?? '');
                if (!empty($nombreEjer)) {
                    $textoFinal .= "• {$nombreEjer}\n";
                    $series = $ejer['series'] ?? [];
                    
                    foreach ($series as $index => $serieData) {
                        $reps = !empty($serieData['reps']) ? $serieData['reps'] : '12';
                        $kilos = !empty($serieData['kilos']) ? $serieData['kilos'] : '0';
                        $numSerie = $index + 1;
                        $textoFinal .= "  - Serie {$numSerie}: {$reps} Reps x {$kilos} kg\n";
                    }
                }
            }

            $rutina->setEjercicios(trim($textoFinal));
            
            $em->flush();

            $this->addFlash('success', 'Rutina actualizada correctamente.');
            return $this->redirectToRoute('app_rutinas');
        }

        // LECTURA DINÁMICA: Reconstruimos la jerarquía visual mapeando el texto plano de la base de datos
        $ejerciciosArray = [];
        $lineas = explode("\n", $rutina->getEjercicios() ?? '');
        $currentEjerIndex = -1;

        foreach ($lineas as $linea) {
            // Identificamos si es una cabecera de Ejercicio (comienza por •)
            if (str_starts_with(trim($linea), '• ')) {
                $currentEjerIndex++;
                $ejerciciosArray[$currentEjerIndex] = [
                    'nombre' => trim(substr(trim($linea), 2)),
                    'series' => []
                ];
            } 
            // Identificamos si es una de sus series anidadas
            elseif (str_contains($linea, ' - Serie ') && $currentEjerIndex >= 0) {
                if (preg_match('/: (.*) Reps x (.*) kg/', $linea, $matches)) {
                    $ejerciciosArray[$currentEjerIndex]['series'][] = [
                        'reps' => trim($matches[1]),
                        'kilos' => trim($matches[2])
                    ];
                }
            }
        }

        // Si la rutina está vacía o el formato antiguo no coincide, inicializamos un ejercicio con 1 serie por defecto
        if (empty($ejerciciosArray)) {
            $ejerciciosArray[] = [
                'nombre' => '',
                'series' => [['reps' => '12', 'kilos' => '20']]
            ];
        }

        return $this->render('rutina/edit.html.twig', [
            'rutina' => $rutina,
            'ejercicios_array' => $ejerciciosArray
        ]);
    }

    /**
     * Elimina una rutina
     */
    #[Route('/{id}/eliminar', name: 'app_rutina_delete', methods: ['POST'])]
    public function delete(Request $request, Rutina $rutina, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rutina->getId(), $request->request->get('_token'))) {
            $em->remove($rutina);
            $em->flush();
            $this->addFlash('success', 'Rutina eliminada correctamente.');
        }

        return $this->redirectToRoute('app_rutinas');
    }
}