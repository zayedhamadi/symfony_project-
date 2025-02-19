<?php

namespace App\Entity;

use App\Repository\MatiereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MatiereRepository::class)]
class Matiere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?int $coefficient = null;

    /**
     * @var Collection<int, Eleve>
     */
    #[ORM\ManyToMany(targetEntity: Eleve::class, inversedBy: 'matieres')]
    private Collection $ideleve;

    #[ORM\ManyToOne(inversedBy: 'matieres')]
    private ?User $idEnsg = null;

    /**
     * @var Collection<int, Quiz>
     */
    #[ORM\OneToMany(targetEntity: Quiz::class, mappedBy: 'matiere')]
    private Collection $quizzes;

    public function __construct()
    {
        $this->ideleve = new ArrayCollection();
        $this->quizzes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCoefficient(): ?int
    {
        return $this->coefficient;
    }

    public function setCoefficient(int $coefficient): static
    {
        $this->coefficient = $coefficient;

        return $this;
    }

    /**
     * @return Collection<int, Eleve>
     */
    public function getIdeleve(): Collection
    {
        return $this->ideleve;
    }

    public function addIdeleve(Eleve $ideleve): static
    {
        if (!$this->ideleve->contains($ideleve)) {
            $this->ideleve->add($ideleve);
        }

        return $this;
    }

    public function removeIdeleve(Eleve $ideleve): static
    {
        $this->ideleve->removeElement($ideleve);

        return $this;
    }

    public function getIdEnsg(): ?User
    {
        return $this->idEnsg;
    }

    public function setIdEnsg(?User $idEnsg): static
    {
        $this->idEnsg = $idEnsg;

        return $this;
    }

    /**
     * @return Collection<int, Quiz>
     */
    public function getQuizzes(): Collection
    {
        return $this->quizzes;
    }

    public function addQuiz(Quiz $quiz): static
    {
        if (!$this->quizzes->contains($quiz)) {
            $this->quizzes->add($quiz);
            $quiz->setMatiere($this);
        }

        return $this;
    }

    public function removeQuiz(Quiz $quiz): static
    {
        if ($this->quizzes->removeElement($quiz)) {
            // Set the owning side to null (unless already changed)
            if ($quiz->getMatiere() === $this) {
                $quiz->setMatiere(null);
            }
        }

        return $this;
    }
}