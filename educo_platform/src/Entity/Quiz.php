<?php

namespace App\Entity;

use App\Repository\QuizRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: QuizRepository::class)]
class Quiz
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Le titre ne peut pas être vide.")]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "Le titre doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères."
    )]
    private string $titre;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: "La description ne peut pas être vide.")]
    #[Assert\Length(
        min: 10,
        minMessage: "La description doit contenir au moins {{ limit }} caractères."
    )]
    private string $description;

    #[ORM\ManyToOne(targetEntity: Cours::class, inversedBy: 'quizzes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Le cours ne peut pas être nul.")]
    private Cours $cours;

    #[ORM\OneToMany(targetEntity: Question::class, mappedBy: 'quiz', cascade: ['persist', 'remove'])]
    #[Assert\Valid] // Valide également les objets Question dans la collection
    private Collection $questions;

    #[ORM\OneToMany(targetEntity: Note::class, mappedBy: 'quiz')]
    private Collection $notes;

    // New ManyToOne relationship with Classe
    #[ORM\ManyToOne(targetEntity: Classe::class, inversedBy: 'quizzes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "La classe ne peut pas être nulle.")]
    private Classe $classe;

    // New relationship with Matiere
    #[ORM\ManyToOne(targetEntity: Matiere::class, inversedBy: 'quizzes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "La matière ne peut pas être nulle.")]
    private Matiere $matiere;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotNull(message: "La date d'ajout ne peut pas être nulle.")]
    private \DateTimeInterface $dateAjout;


    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->dateAjout = new \DateTime(); // Définit automatiquement la date actuelle

    }
    

    // Getters et Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getCours(): ?Cours
    {
        return $this->cours;
    }

    public function setCours(?Cours $cours): self
    {
        $this->cours = $cours;
        return $this;
    }

    /**
     * @return Collection|Question[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setQuiz($this);
        }
        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->removeElement($question)) {
            if ($question->getQuiz() === $this) {
                $question->setQuiz(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|Note[]
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setQuiz($this);
        }
        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
            if ($note->getQuiz() === $this) {
                $note->setQuiz(null);
            }
        }
        return $this;
    }

    // New getters and setters for Classe
    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): self
    {
        $this->classe = $classe;
        return $this;
    }

    // New getters and setters for Matiere
    public function getMatiere(): ?Matiere
    {
        return $this->matiere;
    }

    public function setMatiere(?Matiere $matiere): self
    {
        $this->matiere = $matiere;
        return $this;
    }
    public function getDateAjout(): ?\DateTimeInterface
{
    return $this->dateAjout;
}

public function setDateAjout(\DateTimeInterface $dateAjout): self
{
    $this->dateAjout = $dateAjout;
    return $this;
}

}