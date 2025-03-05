<?php

namespace App\Entity;

use App\Repository\CoursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CoursRepository::class)]
class Cours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)] // New 'name' attribute
    #[Assert\Callback([Matiere::class, 'validateFirstLetterUppercase'])]
    #[Assert\NotBlank(message: "The subject name cannot be blank.")] // Add this line
    #[Assert\Length(
        min: 3,
        max: 30,
        minMessage: "The subject name must be at least 3 characters long.",
        maxMessage: "The subject name cannot be longer than 30 characters."
    )]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: Matiere::class, inversedBy: 'cours', cascade: ['persist'])] // REMOVE 'remove'
    #[ORM\JoinColumn(nullable: false, onDelete: 'SET NULL')] // Change CASCADE to SET NULL
    private ?Matiere $IdMatiere = null;

    #[ORM\OneToMany(targetEntity: Quiz::class, mappedBy: 'cours')]
    private Collection $quizzes;

    #[ORM\OneToMany(mappedBy: 'cours', targetEntity: Classe::class)]
    private Collection $classes;

    #[Assert\NotBlank(message: "Le Numero de Chapitre est obligatoire.")]
    #[Assert\Positive(message: "Le  Numero de Chapitre doit être un nombre positif.")]
    #[ORM\Column(type: 'integer')]
    private ?int $chapterNumber = null;



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

    #[Assert\File(
        maxSize: '5M', // Limit file size to 5MB
        mimeTypes: ['application/pdf'], // Only allow PDF files
        mimeTypesMessage: 'Please upload a valid PDF file.'
    )]
    private $pdfFile;


//    public function __construct()
//    {
//        $this->IdUser = new ArrayCollection();
//    }
    #[ORM\ManyToOne(targetEntity: Classe::class, inversedBy: 'cours')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Classe $classe = null;

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): static
    {
        $this->classe = $classe;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChapterNumber(): ?int
    {
        return $this->chapterNumber;
    }

    public function setChapterNumber(int $chapterNumber): self
    {
        $this->chapterNumber = $chapterNumber;
        return $this;
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

    public static function validateFirstLetterUppercase($nom, ExecutionContextInterface $context)
    {
        if ($nom && !ctype_upper($nom[0])) {
            $context->buildViolation('The subject name must start with an uppercase letter.')
                ->atPath('nom')
                ->addViolation();
        }
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