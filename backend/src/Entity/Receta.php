<?php

namespace App\Entity;

use App\Repository\RecetaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecetaRepository::class)]
class Receta
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titulo = null;

    #[ORM\Column(length: 100)]
    private ?string $categoria = null;

    // CORREGIDO: Cambiado de string(255) a text para permitir textos largos
    #[ORM\Column(type: Types::TEXT)]
    private ?string $descripcion = null;

    // AÑADIDO: Lista de ingredientes en formato text
    #[ORM\Column(type: Types::TEXT)]
    private ?string $ingredientes = null;

    // AÑADIDO: Macronutrientes y tiempos en formato entero (integer)
    #[ORM\Column]
    private ?int $proteinas = null;

    #[ORM\Column]
    private ?int $kcal = null;

    #[ORM\Column]
    private ?int $tiempo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): static
    {
        $this->titulo = $titulo;
        return $this;
    }

    public function getCategoria(): ?string
    {
        return $this->categoria;
    }

    public function setCategoria(string $categoria): static
    {
        $this->categoria = $categoria;
        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): static
    {
        $this->descripcion = $descripcion;
        return $this;
    }

    public function getIngredientes(): ?string
    {
        return $this->ingredientes;
    }

    public function setIngredientes(string $ingredientes): static
    {
        $this->ingredientes = $ingredientes;
        return $this;
    }

    public function getProteinas(): ?int
    {
        return $this->proteinas;
    }

    public function setProteinas(int $proteinas): static
    {
        $this->proteinas = $proteinas;
        return $this;
    }

    public function getKcal(): ?int
    {
        return $this->kcal;
    }

    public function setKcal(int $kcal): static
    {
        $this->kcal = $kcal;
        return $this;
    }

    public function getTiempo(): ?int
    {
        return $this->tiempo;
    }

    public function setTiempo(int $tiempo): static
    {
        $this->tiempo = $tiempo;
        return $this;
    }
}