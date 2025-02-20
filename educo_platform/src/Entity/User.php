<?php

namespace App\Entity;

use App\Entity\Enum\Rolee;
use DateTimeInterface;
use App\Entity\Enum\EtatCompte;
use App\Entity\Enum\Genre;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_PhoneNumber', fields: ['num_tel'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image = null;


    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: "L'email est obligatoire")]
    #[Assert\Email(message: "Veuillez entrer une adresse email valide.")]
    private ?string $email = null;


    #[ORM\Column(length: 180, nullable: true)]
    #[Assert\NotBlank(message: "Le nom est obligatoire")]
    #[Assert\Length(min: 2, minMessage: "Le nom doit contenir au moins {{ limit }} caractères.")]
    #[Assert\Regex(pattern: "/^[^0-9]*$/", message: "Le nom ne doit pas contenir de chiffres.")]

    private ?string $nom = null;


    #[ORM\Column(length: 180, nullable: true)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire")]
    #[Assert\Length(min: 2, minMessage: "Le prénom doit contenir au moins {{ limit }} caractères.")]
    #[Assert\Regex(pattern: "/^[^0-9]*$/", message: "Le prénom ne doit pas contenir de chiffres.")]
    private ?string $prenom = null;


    #[ORM\Column(length: 180, nullable: true)]
    #[Assert\NotBlank(message: "L'adresse est obligatoire")]
    private ?string $adresse = null;

    #[ORM\Column(length: 300, nullable: true)]
    #[Assert\NotBlank(message: "La description est obligatoire")]
    #[Assert\Length(max: 500, maxMessage: "La description ne doit pas dépasser {{ limit }} caractères.")]
    private ?string $description = null;


    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(message: "Le numéro de téléphone est obligatoire")]
    #[Assert\Length(min: 8, max: 8, exactMessage: "Le numéro de téléphone doit contenir {{ limit }} caractères.")]
    #[Assert\Regex(pattern: "/^(5|2|9)\d{7}$/", message: "Le numéro de téléphone doit commencer par 5, 2 ou 9.")]
    private ?int $num_tel = null;

    #[ORM\Column(type: 'string', nullable: true, enumType: Genre::class)]
    #[Assert\NotBlank(message: "Le genre est obligatoire")]
    private ?Genre $genre = null;

    #[ORM\Column(type: 'string', nullable: true, enumType: EtatCompte::class)]
    #[Assert\NotBlank(message: "L'état du compte est obligatoire")]
    private ?EtatCompte $etatCompte = null;


    #[ORM\Column(type: 'date', nullable: true)]
    #[Assert\NotBlank(message: "La date de naissance est obligatoire.")]
    #[Assert\LessThanOrEqual("today", message: "La date de naissance ne peut pas être supérieure à la date actuelle.")]
    #[Assert\Callback(callback: 'validateAge')]
    private ?DateTimeInterface $dateNaissance = null;

    public function validateAge(ExecutionContextInterface $context): void
    {
        $today = new \DateTime();
        $minAge = 18;

        if (!$this->dateNaissance) {
            $context->buildViolation('La date de naissance est obligatoire.')
                ->atPath('dateNaissance')
                ->addViolation();
            return;
        }

        if ($this->dateNaissance > $today) {
            $context->buildViolation('La date de naissance ne peut pas être supérieure à la date actuelle.')
                ->atPath('dateNaissance')
                ->addViolation();
            return;
        }

        $age = $today->diff($this->dateNaissance)->y;
        if ($age < $minAge) {
            $context->buildViolation('L\'utilisateur doit avoir au moins 18 ans.')
                ->atPath('dateNaissance')
                ->addViolation();
        }
    }


    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank(message: "Le mot de passe est obligatoire")]
    #[Assert\Regex(pattern: "/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_]).+$/", message: "Le mot de passe doit contenir au minimum un chiffre, une lettre et un caractère spécial.")]
    private ?string $password = null;

    /**
     * @var Collection<int, Classe>
     */
    #[ORM\ManyToMany(targetEntity: Classe::class, mappedBy: 'id_user')]
    private Collection $classes;

    #[ORM\OneToMany(targetEntity: Reclamation::class, mappedBy: 'user')]
    private Collection $reclamations;


    /**
     * @var Collection<int, Eleve>
     */
    #[ORM\OneToMany(targetEntity: Eleve::class, mappedBy: 'IdParent')]
    private Collection $eleves;

    /**
     * @var Collection<int, Matiere>
     */
    #[ORM\OneToMany(targetEntity: Matiere::class, mappedBy: 'idEnsg')]
    private Collection $matieres;


    /**
     * @var Collection<int, Commande>
     */
    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'parent')]
    private Collection $commandes;
    /**
     * @var Rolee[] The user roles
     */
    #[ORM\Column(type: "json")]
    #[Assert\NotBlank(message: "Le role  est obligatoire")]
    private array $roles = [];


    public function __construct()
    {
        $this->classes = new ArrayCollection();
        $this->eleves = new ArrayCollection();
        $this->matieres = new ArrayCollection();
        $this->commandes = new ArrayCollection();
        $this->reclamations = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }

//    #[ORM\ManyToOne(inversedBy: 'IdUser')]
//    private ?Cours $cours = null;

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): void
    {
        $this->prenom = $prenom;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @return Rolee[]
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        if (empty(array_intersect($roles, [Rolee::Admin->value]))) {
            return $roles;
        }

        return array_unique($roles);
    }

    /**
     * @param Rolee[] $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = array_map(fn($role) => $role instanceof Rolee ? $role->value : $role, $roles);

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->nom . ' ' . $this->prenom;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
            $class->addIdUser($this);
        }

        return $this;
    }

    public function removeClass(Classe $class): static
    {
        if ($this->classes->removeElement($class)) {
            $class->removeIdUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Reclamation>
     */
    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }

    public function addReclamation(Reclamation $reclamation): static
    {
        if (!$this->reclamations->contains($reclamation)) {
            $this->reclamations->add($reclamation);
            $reclamation->setUser($this);
        }

        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): static
    {
        if ($this->reclamations->removeElement($reclamation)) {
            // set the owning side to null (unless already changed)
            if ($reclamation->getUser() === $this) {
                $reclamation->setUser(null);
            }
        }

        return $this;
    }


    public function getReclamation(): ?Reclamation
    {
        return $this->reclamation;
    }

    public function setReclamation(?Reclamation $reclamation): static
    {
        $this->reclamation = $reclamation;

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
            $elefe->setIdParent($this);
        }

        return $this;
    }

    public function removeElefe(Eleve $elefe): static
    {
        if ($this->eleves->removeElement($elefe)) {
            // set the owning side to null (unless already changed)
            if ($elefe->getIdParent() === $this) {
                $elefe->setIdParent(null);
            }
        }

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
            $matiere->setIdEnsg($this);
        }

        return $this;
    }

    public function removeMatiere(Matiere $matiere): static
    {
        if ($this->matieres->removeElement($matiere)) {
            // set the owning side to null (unless already changed)
            if ($matiere->getIdEnsg() === $this) {
                $matiere->setIdEnsg(null);
            }
        }

        return $this;
    }

//    public function getCours(): ?Cours
//    {
//        return $this->cours;
//    }
//
//    public function setCours(?Cours $cours): static
//    {
//        $this->cours = $cours;
//
//        return $this;
//    }


    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): void
    {
        $this->adresse = $adresse;
    }

    public function getNumTel(): ?int
    {
        return $this->num_tel;
    }

    public function setNumTel(?int $num_tel): void
    {
        $this->num_tel = $num_tel;
    }

    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    public function setGenre(?Genre $genre): void
    {
        $this->genre = $genre;
    }

    public function getEtatCompte(): ?EtatCompte
    {
        return $this->etatCompte;

    }

    public function setEtatCompte(?EtatCompte $etatCompte): void
    {
        $this->etatCompte = $etatCompte;
    }

    /**
     * @return Collection<int, Commande>
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): static
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setParent($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getParent() === $this) {
                $commande->setParent(null);
            }
        }

        return $this;
    }


    public function getDateNaissance(): ?DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?DateTimeInterface $dateNaissance): void
    {
        $this->dateNaissance = $dateNaissance;
    }


}
