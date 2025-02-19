<?php

namespace App\Entity;

use App\Repository\ClasseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClasseRepository::class)]
class Classe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de la classe ne peut pas être vide.")]
    #[Assert\Length(max: 100, maxMessage: "Le nom de la classe ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $nom_classe = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le numéro de salle ne peut pas être vide.")]
    private ?int $Num_salle = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La capacité maximale ne peut pas être vide.")]
    #[Assert\GreaterThan(value: 0, message: "La capacité maximale doit être supérieure à {{ compared_value }}.")]
    private ?int $capacite_max = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'classes')]
    #[Assert\Count(min: 1, minMessage: 'Veuillez sélectionner au moins un utilisateur.')]  
    private Collection $id_user;

    /**
     * @var Collection<int, Eleve>
     */
    #[ORM\OneToMany(targetEntity: Eleve::class, mappedBy: 'IdClasse')]
    private Collection $eleves;

    public function __construct()
    {
        $this->id_user = new ArrayCollection();
        $this->eleves = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomClasse(): ?string
    {
        return $this->nom_classe;
    }

    public function setNomClasse(string $nom_classe): static
    {
        $this->nom_classe = $nom_classe;

        return $this;
    }

    public function getNumSalle(): ?int
    {
        return $this->Num_salle;
    }

    public function setNumSalle(int $Num_salle): static
    {
        $this->Num_salle = $Num_salle;

        return $this;
    }

    public function getCapaciteMax(): ?int
    {
        return $this->capacite_max;
    }

    public function setCapaciteMax(int $capacite_max): static
    {
        $this->capacite_max = $capacite_max;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getIdUser(): Collection
    {
        return $this->id_user;
    }

    public function addIdUser(User $idUser): static
    {
        if (!$this->id_user->contains($idUser)) {
            $this->id_user->add($idUser);
        }

        return $this;
    }

    public function removeIdUser(User $idUser): static
    {
        $this->id_user->removeElement($idUser);

        return $this;
    }

    /**
     * @return Collection<int, Eleve>
     */
    public function getEleves(): Collection
    {
        return $this->eleves;
    }

    public function addElefe(Eleve $elefe): static
    {
        if (!$this->eleves->contains($elefe)) {
            $this->eleves->add($elefe);
            $elefe->setIdClasse($this);
        }

        return $this;
    }

    public function removeElefe(Eleve $elefe): static
    {
        if ($this->eleves->removeElement($elefe)) {
            // set the owning side to null (unless already changed)
            if ($elefe->getIdClasse() === $this) {
                $elefe->setIdClasse(null);
            }
        }

        return $this;
    }
}
