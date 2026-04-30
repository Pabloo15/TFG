<?php

namespace App\Entity;

use App\Repository\ClaseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClaseRepository::class)]
class Clase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)] // Especificamos que es fecha y hora
    private ?\DateTimeInterface $horario = null;

    #[ORM\Column]
    private ?int $aforoMaximo = null;

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

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): static
    {
        $this->descripcion = $descripcion;
        return $this;
    }

    public function getHorario(): ?\DateTimeInterface
    {
        return $this->horario;
    }

    public function setHorario(\DateTimeInterface $horario): static
    {
        $this->horario = $horario;
        return $this;
    }

    public function getAforoMaximo(): ?int
    {
        return $this->aforoMaximo;
    }

    public function setAforoMaximo(int $aforoMaximo): static
    {
        $this->aforoMaximo = $aforoMaximo;
        return $this;
    }
}
