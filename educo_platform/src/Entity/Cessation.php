<?php

namespace App\Entity;

use App\Repository\CessationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CessationRepository::class)]
class Cessation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $motif = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateMotif = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?User $idUser = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): static
    {
        $this->motif = $motif;

        return $this;
    }

    public function getDateMotif(): ?\DateTimeInterface
    {
        return $this->dateMotif;
    }

    public function setDateMotif(\DateTimeInterface $dateMotif): static
    {
        $this->dateMotif = $dateMotif;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }
}
