<?php

namespace App\Entity;

use App\Repository\CoursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoursRepository::class)]
class Cours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Matiere $IdMatiere = null;

    #[ORM\OneToMany(targetEntity: Quiz::class, mappedBy: 'cours')]
    private Collection $quizzes;

    public function __construct()
    {
        $this->quizzes = new ArrayCollection();
    }
    /**
     * @var Collection<int, Classe>
     */
    #[ORM\OneToMany(mappedBy: 'cours', targetEntity: Classe::class)]
    private Collection $classes;

    public function __constructt()
    {
        $this->classes = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdMatiere(): ?Matiere
    {
        return $this->IdMatiere;
    }

    public function setIdMatiere(?Matiere $IdMatiere): static
    {
        $this->IdMatiere = $IdMatiere;

        return $this;
    }

  
}
