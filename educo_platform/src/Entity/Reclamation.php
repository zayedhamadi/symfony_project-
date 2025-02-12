<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateDeCreation = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'reclamation')]
    private Collection $idUser;

    /**
     * @var Collection<int, HistoriqueReclamation>
     */
    #[ORM\OneToMany(targetEntity: HistoriqueReclamation::class, mappedBy: 'refReclamtion')]
    private Collection $historiqueReclamations;

    public function __construct()
    {
        $this->idUser = new ArrayCollection();
        $this->historiqueReclamations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDateDeCreation(): ?\DateTimeInterface
    {
        return $this->dateDeCreation;
    }

    public function setDateDeCreation(\DateTimeInterface $dateDeCreation): static
    {
        $this->dateDeCreation = $dateDeCreation;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getIdUser(): Collection
    {
        return $this->idUser;
    }

    public function addIdUser(User $idUser): static
    {
        if (!$this->idUser->contains($idUser)) {
            $this->idUser->add($idUser);
            $idUser->setReclamation($this);
        }

        return $this;
    }

    public function removeIdUser(User $idUser): static
    {
        if ($this->idUser->removeElement($idUser)) {
            // set the owning side to null (unless already changed)
            if ($idUser->getReclamation() === $this) {
                $idUser->setReclamation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, HistoriqueReclamation>
     */
    public function getHistoriqueReclamations(): Collection
    {
        return $this->historiqueReclamations;
    }

    public function addHistoriqueReclamation(HistoriqueReclamation $historiqueReclamation): static
    {
        if (!$this->historiqueReclamations->contains($historiqueReclamation)) {
            $this->historiqueReclamations->add($historiqueReclamation);
            $historiqueReclamation->setRefReclamtion($this);
        }

        return $this;
    }

    public function removeHistoriqueReclamation(HistoriqueReclamation $historiqueReclamation): static
    {
        if ($this->historiqueReclamations->removeElement($historiqueReclamation)) {
            // set the owning side to null (unless already changed)
            if ($historiqueReclamation->getRefReclamtion() === $this) {
                $historiqueReclamation->setRefReclamtion(null);
            }
        }

        return $this;
    }
}
