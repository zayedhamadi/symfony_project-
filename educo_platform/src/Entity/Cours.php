<?php

namespace App\Entity;

use App\Repository\CoursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
#[ORM\Entity(repositoryClass: CoursRepository::class)]
class Cours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)] // New 'name' attribute
    private ?string $name = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\ManyToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Matiere $IdMatiere = null;

    #[ORM\OneToMany(targetEntity: Quiz::class, mappedBy: 'cours')]
    private Collection $quizzes;

    #[ORM\OneToMany(mappedBy: 'cours', targetEntity: Classe::class)]
    private Collection $classes;
    


    //    /**
//     * @var Collection<int, User>
//     */
//    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'cours')]
//    private Collection $IdUser;

    public function __construct()
    {
        $this->quizzes = new ArrayCollection();
        $this->classes = new ArrayCollection();
    }
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $pdfFilename = null;
//    public function __construct()
//    {
//        $this->IdUser = new ArrayCollection();
//    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
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

    /**
     * @return Collection<int, Quiz>
     */
    public function getQuizzes(): Collection{
        return $this->quizzes;
    }

  




    public function getPdfFilename(): ?string{
        return $this->pdfFilename;
    }

    public function addQuiz(Quiz $quiz): static
    {
        if (!$this->quizzes->contains($quiz)) {
            $this->quizzes->add($quiz);
            $quiz->setCours($this);
        }

        return $this;
    }

    public function removeQuiz(Quiz $quiz): static
    {
        if ($this->quizzes->removeElement($quiz)) {
            // Set the owning side to null (unless already changed)
            if ($quiz->getCours() === $this) {
                $quiz->setCours(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Classe>
     */
    public function getClasses(): Collection
    {
        return $this->classes;
    }

    public function addClass(Classe $class): static
    {
        if (!$this->classes->contains($class)) {
            $this->classes->add($class);
            $class->setCours($this);
        }
        return $this;
    }
    public function setPdfFilename(?string $pdfFilename): self
    {
        $this->pdfFilename = $pdfFilename;
        return $this;
    }
    

    public function removeClass(Classe $class): static
    {if ($this->classes->removeElement($class)) {
        // Set the owning side to null (unless already changed)
        if ($class->getCours() === $this) {
            $class->setCours(null);
        }
    }
        return $this;
    }
    

    
//    /**
//     * @return Collection<int, User>
//     */
//    public function getIdUser(): Collection
//    {
//        return $this->IdUser;
//    }
//
//    public function addIdUser(User $idUser): static
//    {
//        if (!$this->IdUser->contains($idUser)) {
//            $this->IdUser->add($idUser);
//            $idUser->setCours($this);
//        }
//
//        return $this;
//    }
//
//    public function removeIdUser(User $idUser): static
//    {
//        if ($this->IdUser->removeElement($idUser)) {
//            // set the owning side to null (unless already changed)
//            if ($idUser->getCours() === $this) {
//                $idUser->setCours(null);
//            }
//        }
//
//        return $this;
//    }
}