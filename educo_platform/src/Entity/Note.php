<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: NoteRepository::class)]
class Note
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank(message: 'Le score ne peut pas être vide.')]
    #[Assert\Type(type: 'float', message: 'Le score doit être un nombre.')]
    #[Assert\Range(
        min: 0,
        max: 20,
        notInRangeMessage: 'Le score doit être compris entre {{ min }} et {{ max }}.'
    )]
    private float $score;

    #[ORM\ManyToOne(targetEntity: Eleve::class, inversedBy: 'notes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Un élève doit être associé à la note.')]
    private Eleve $eleve;

    #[ORM\ManyToOne(targetEntity: Quiz::class, inversedBy: 'notes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Un quiz doit être associé à la note.')]
    private Quiz $quiz;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(float $score): self
    {
        $this->score = $score;
        return $this;
    }

    public function getEleve(): ?Eleve
    {
        return $this->eleve;
    }

    public function setEleve(?Eleve $eleve): self
    {
        $this->eleve = $eleve;
        return $this;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): self
    {
        $this->quiz = $quiz;
        return $this;
    }
}