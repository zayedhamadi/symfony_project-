<?php

namespace App\Entity;

use App\Repository\EleveRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EleveRepository::class)]
class Eleve
{
    #[ORM\Id]  
    #[ORM\GeneratedValue]  
    #[ORM\Column]  
    private ?int $id = null;  

    #[ORM\Column(length: 255)]  
    #[Assert\NotBlank(message: "Le nom de l'élève ne peut pas être vide.")]
    #[Assert\Length(max: 50, maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $Nom = null;  

    #[ORM\Column(length: 255)]  
    #[Assert\NotBlank(message: "Le prénom de l'élève ne peut pas être vide.")]
    #[Assert\Length(max: 50, maxMessage: "Le prénom ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $Prenom = null;  

    #[ORM\Column(type: Types::DATE_MUTABLE)]  
    #[Assert\NotBlank(message: "La date de naissance ne peut pas être vide.")]
    private ?\DateTimeInterface $DateDeNaissance = null;  

    #[ORM\ManyToOne(inversedBy: 'eleves')]  
    #[Assert\NotNull(message: "Veuillez sélectionner une classe.")]  
    private ?Classe $IdClasse = null;  

    #[ORM\ManyToOne(inversedBy: 'eleves')]  
    #[Assert\NotNull(message: "Veuillez sélectionner un parent.")]  
    private ?User $IdParent = null;  

    #[ORM\Column]
    #[Assert\NotBlank(message: "La moyenne ne peut pas être vide.")]
    #[Assert\GreaterThanOrEqual(value: 0, message: "La moyenne doit être positive.")]
    #[Assert\LessThanOrEqual(value: 20, message: "La moyenne ne peut pas dépasser 20.")]
    private ?float $moyenne = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le nombre d'absences ne peut pas être vide.")]
    #[Assert\GreaterThanOrEqual(value: 0, message: "Le nombre d'absences ne peut pas être négatif.")]
    private ?int $NbreAbscence = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date d'inscription ne peut pas être vide.")]
    private ?\DateTimeInterface $DateInscription = null;

    /**
     * @var Collection<int, Matiere>
     */
    #[ORM\ManyToMany(targetEntity: Matiere::class, mappedBy: 'ideleve')]
    private Collection $matieres;

    public function __construct()
    {
        $this->matieres = new ArrayCollection();
        $this->moyenne = 0.0; // Valeur par défaut
        $this->NbreAbscence = 0; // Valeur par défaut
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): static
    {
        $this->Nom = $Nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->Prenom;
    }

    public function setPrenom(string $Prenom): static
    {
        $this->Prenom = $Prenom;
        return $this;
    }

    public function getDateDeNaissance(): ?\DateTimeInterface
    {
        return $this->DateDeNaissance;
    }

    public function setDateDeNaissance(\DateTimeInterface $DateDeNaissance): static
    {
        $this->DateDeNaissance = $DateDeNaissance;
        return $this;
    }

    public function getIdClasse(): ?Classe
    {
        return $this->IdClasse;
    }

    public function setIdClasse(?Classe $IdClasse): static
    {
        $this->IdClasse = $IdClasse;
        return $this;
    }

    public function getIdParent(): ?User
    {
        return $this->IdParent;
    }

    public function setIdParent(?User $IdParent): static
    {
        $this->IdParent = $IdParent;
        return $this;
    }

    public function getMoyenne(): ?float
    {
        return $this->moyenne;
    }

    public function setMoyenne(float $moyenne): static
    {
        $this->moyenne = $moyenne;
        return $this;
    }

    public function getNbreAbscence(): ?int
    {
        return $this->NbreAbscence;
    }

    public function setNbreAbscence(int $NbreAbscence): static
    {
        $this->NbreAbscence = $NbreAbscence;
        return $this;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->DateInscription;
    }

    public function setDateInscription(\DateTimeInterface $DateInscription): static
    {
        $this->DateInscription = $DateInscription;
        return $this;
    }

    /**
     * @return Collection<int, Matiere>
     */
    public function getMatieres(): Collection
    {
        return $this->matieres;
    }

    public function addMatiere(Matiere $matiere): static
    {
        if (!$this->matieres->contains($matiere)) {
            $this->matieres->add($matiere);
            $matiere->addIdeleve($this);
        }
        return $this;
    }

    public function removeMatiere(Matiere $matiere): static
    {
        if ($this->matieres->removeElement($matiere)) {
            $matiere->removeIdeleve($this);
        }
        return $this;
    }
}
