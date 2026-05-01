<?php

namespace App\Entity;

use App\Repository\RutinaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RutinaRepository::class)]
class Rutina
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $ejercicios = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getEjercicios(): ?string
    {
        return $this->ejercicios;
    }

    public function setEjercicios(string $ejercicios): static
    {
        $this->ejercicios = $ejercicios;

        return $this;
    }
}
