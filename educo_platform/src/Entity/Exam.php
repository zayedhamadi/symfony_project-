<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Exam
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Subject cannot be blank.')]
    private ?string $subject = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank(message: 'Start time cannot be blank.')]
    #[Assert\GreaterThanOrEqual(
        value: 'now',
        message: 'The start time cannot be in the past.'
    )]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank(message: 'End time cannot be blank.')]
    #[Assert\GreaterThan(propertyPath: 'startTime',
        message: 'End time must be after start time.')]
    private ?\DateTimeInterface $endTime = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'You must type a Classeroom')]

    private ?string $location = null;

    #[ORM\ManyToOne(targetEntity: Classe::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Class cannot be blank.')]
    private ?Classe $classe = null;
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;
        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;
        return $this;
    }

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): self
    {
        $this->classe = $classe;
        return $this;
    }
}
