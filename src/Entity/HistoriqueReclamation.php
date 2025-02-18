<?php

namespace App\Entity;

use App\Repository\HistoriqueReclamationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoriqueReclamationRepository::class)]
class HistoriqueReclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'historiqueReclamations')]
    private ?Reclamation $refReclamtion = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRefReclamtion(): ?Reclamation
    {
        return $this->refReclamtion;
    }

    public function setRefReclamtion(?Reclamation $refReclamtion): static
    {
        $this->refReclamtion = $refReclamtion;

        return $this;
    }
}
