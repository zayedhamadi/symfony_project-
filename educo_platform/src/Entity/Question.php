<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'Le texte de la question ne peut pas être vide.')]
    #[Assert\Length(
        min: 5,
        max: 500,
        minMessage: 'La question doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'La question ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: '/^[\p{L}0-9\s\p{P}]+$/u',
        message: 'Le texte de la question ne peut contenir que des lettres, des chiffres et des signes de ponctuation.'
    )]
    private string $texte;

    #[ORM\Column(type: 'json')]
    #[Assert\NotBlank(message: 'Les options ne peuvent pas être vides.')]
    #[Assert\Count(
        min: 2,
        minMessage: 'Il doit y avoir au moins {{ limit }} options pour la question.'
    )]
    #[Assert\All([
        new Assert\NotBlank(message: 'Une option ne peut pas être vide.'),
        new Assert\Length(
            max: 255,
            maxMessage: 'Une option ne peut pas dépasser {{ limit }} caractères.'
        )
    ])]
    private array $options = [];

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'La réponse ne peut pas être vide.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'La réponse ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[Assert\Choice(
        callback: 'getOptions',
        message: 'La réponse doit être une des options fournies.'
    )]
    private string $reponse;

    #[ORM\ManyToOne(targetEntity: Quiz::class, inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Chaque question doit être associée à un quiz.')]
    private Quiz $quiz;

    public function __construct()
    {
        $this->options = [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(string $texte): self
    {
        $this->texte = $texte;
        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    public function setReponse(string $reponse): self
    {
        if (!in_array($reponse, $this->options, true)) {
            throw new \InvalidArgumentException('La réponse doit être une des options fournies.');
        }
        $this->reponse = $reponse;
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
