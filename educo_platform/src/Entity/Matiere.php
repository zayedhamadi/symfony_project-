<?php

namespace App\Entity;

use App\Repository\MatiereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

//#[ORM\Table(uniqueConstraints: [
//    new UniqueConstraint(columns: ["name", "idEnsg"])
//])]
#[ORM\Entity(repositoryClass: MatiereRepository::class)]
class Matiere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255 , unique: true)]
    #[Assert\NotBlank(message: "The subject name cannot be blank.")] // Add this line
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: "The subject name must be at least {{ 3 }} characters long.",
        maxMessage: "The subject name cannot be longer than {{ 50 }} characters."
    )]
    private ?string $nom = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le coefficient est obligatoire.")]
    #[Assert\Positive(message: "Le coefficient doit être un nombre positif.")]
    #[Assert\LessThanOrEqual(
        value: 10,
        message: "Le coefficient ne peut pas dépasser 10.")]
    #[Assert\Type(
        type: 'integer',
        message: "The coefficient must be a valid number."
    )] // Add this line
    #[Assert\Positive(message: "The coefficient must be a positive number.")] // Add this line
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

    #[ORM\OneToMany(targetEntity: Cours::class, mappedBy: 'IdMatiere', cascade: ['remove'], orphanRemoval: true)]
    private Collection $cours;
    public function __construct()
    {
        $this->ideleve = new ArrayCollection();
        $this->quizzes = new ArrayCollection();
        $this->cours = new ArrayCollection();

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


    /**
     * @return Collection<int, Cours>
     */
    public function getCours(): Collection
    {
        return $this->cours;
    }

    public function addCours(Cours $cours): self
    {
        if (!$this->cours->contains($cours)) {
            $this->cours[] = $cours;
            $cours->setIdMatiere($this);
        }

        return $this;
    }

    public function removeCours(Cours $cours): self
    {
        if ($this->cours->removeElement($cours)) {
            // Set the owning side to null (unless already changed)
            if ($cours->getIdMatiere() === $this) {
                $cours->setIdMatiere(null);
            }
        }

        return $this;
    }
}
