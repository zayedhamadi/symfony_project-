<?php

namespace App\Entity;

use App\Repository\CoursRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoursRepository::class)]
class Cours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\ManyToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Matiere $IdMatiere = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null; // New name field
//    /**
//     * @var Collection<int, User>
//     */
//    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'cours')]
//    private Collection $IdUser;

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


    public function getIdMatiere(): ?Matiere
    {
        return $this->IdMatiere;
    }

    public function setIdMatiere(?Matiere $IdMatiere): static
    {
        $this->IdMatiere = $IdMatiere;

        return $this;
    }

    public function getPdfFilename(): ?string
    {
        return $this->pdfFilename;
    }

    public function setPdfFilename(?string $pdfFilename): self
    {
        $this->pdfFilename = $pdfFilename;
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
