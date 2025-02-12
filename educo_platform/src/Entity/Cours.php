<?php

namespace App\Entity;

use App\Repository\CoursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoursRepository::class)]
class Cours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Matiere $IdMatiere = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'cours')]
    private Collection $IdUser;

    public function __construct()
    {
        $this->IdUser = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, User>
     */
    public function getIdUser(): Collection
    {
        return $this->IdUser;
    }

    public function addIdUser(User $idUser): static
    {
        if (!$this->IdUser->contains($idUser)) {
            $this->IdUser->add($idUser);
            $idUser->setCours($this);
        }

        return $this;
    }

    public function removeIdUser(User $idUser): static
    {
        if ($this->IdUser->removeElement($idUser)) {
            // set the owning side to null (unless already changed)
            if ($idUser->getCours() === $this) {
                $idUser->setCours(null);
            }
        }

        return $this;
    }
}
