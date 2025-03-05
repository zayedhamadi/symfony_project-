<?php
// src/Entity/Comment.php
namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\HasLifecycleCallbacks] // Add this annotation
#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id=null;

    #[ORM\ManyToOne(targetEntity: Cours::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private $course;


    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 5,
        max: 1000,
        minMessage: 'Your comment must be at least {{ 5 }} characters long.',
        maxMessage: 'Your comment cannot be longer than {{ 1000 }} characters.'
    )]
    private $content;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getCourse(): ?Cours
    {
        return $this->course;
    }


    public function setCourse(?Cours $course): self
    {
        $this->course = $course;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTime();
    }
}
