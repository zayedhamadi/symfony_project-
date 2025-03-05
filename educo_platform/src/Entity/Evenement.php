<?php

namespace App\Entity;

use App\Entity\Enum\EventType;
use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['events'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre est obligatoire.")]
    #[Assert\Length(max: 50, maxMessage: "Le titre ne doit pas dépasser {{ limit }} caractères.")]
    #[Groups(['events'])]
    private ?string $titre = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 500, maxMessage: "La description ne doit pas dépasser {{ limit }} caractères.")]
    #[Groups(['events'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "La date de début est obligatoire.")]
    #[Assert\GreaterThan(
        value: "now",
        message: "La date de début doit être superieur à la date et heure actuelles."
    )]
    #[Groups(['events'])]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\GreaterThan(propertyPath: "dateDebut", message: "La date de fin doit être postérieure à la date de début.")]
    #[Groups(['events'])]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le lieu est obligatoire.")]
    #[Assert\Length(max: 255, maxMessage: "Le lieu ne doit pas dépasser {{ limit }} caractères.")]
    #[Groups(['events'])]
    private ?string $lieu = null;

    #[ORM\Column]
    #[Groups(['events'])]
    private ?bool $inscriptionRequise = false;

    #[ORM\Column(nullable: true)]
    #[Assert\Positive(message: "Le nombre de places doit être un nombre positif.")]
    #[Groups(['events'])]
    private ?int $nombrePlaces = null;

    #[ORM\Column(type: "string", length: 255, enumType: EventType::class)]
    #[Assert\NotNull(message: "Le type d'événement est obligatoire.")]
    #[Groups(['events'])]
    private ?EventType $type = null;

    public function getType(): ?EventType
    {
       return $this->type;
    }

    public function setType(EventType $type): static
    {
       $this->type = $type;
       return $this;
    }

    /**
     * @var Collection<int, InscriptionEvenement>
     */
    #[ORM\OneToMany(targetEntity: InscriptionEvenement::class, mappedBy: 'evenement', cascade: ['remove'])]
    private Collection $inscriptionEvenements;

    public function __construct()
    {
        $this->inscriptionEvenements = new ArrayCollection();
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

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;
        return $this;
    }

    public function isInscriptionRequise(): ?bool
    {
        return $this->inscriptionRequise;
    }

    public function setInscriptionRequise(bool $inscriptionRequise): static
    {
        $this->inscriptionRequise = $inscriptionRequise;
        return $this;
    }

    public function getNombrePlaces(): ?int
    {
        return $this->nombrePlaces;
    }

    public function setNombrePlaces(?int $nombrePlaces): static
    {
        $this->nombrePlaces = $nombrePlaces;
        return $this;
    }

    /**
     * @return Collection<int, InscriptionEvenement>
     */
    public function getInscriptionEvenements(): Collection
    {
        return $this->inscriptionEvenements;
    }

    public function addInscriptionEvenement(InscriptionEvenement $inscriptionEvenement): static
    {
        if (!$this->inscriptionEvenements->contains($inscriptionEvenement)) {
            $this->inscriptionEvenements->add($inscriptionEvenement);
            $inscriptionEvenement->setEvenement($this);
        }
        return $this;
    }

    public function removeInscriptionEvenement(InscriptionEvenement $inscriptionEvenement): static
    {
        if ($this->inscriptionEvenements->removeElement($inscriptionEvenement)) {
            // set the owning side to null (unless already changed)
            if ($inscriptionEvenement->getEvenement() === $this) {
                $inscriptionEvenement->setEvenement(null);
            }
        }
        return $this;
    }
}
