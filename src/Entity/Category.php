<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'categories', targetEntity: Painting::class)]
    private Collection $paintings;

    #[ORM\OneToMany(mappedBy: 'Category', targetEntity: Tutoriel::class)]
    private Collection $tutoriels;

    public function __construct()
    {
        $this->paintings = new ArrayCollection();
        $this->tutoriels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Painting>
     */
    public function getPaintings(): Collection
    {
        return $this->paintings;
    }

    /**
     * @param Painting $painting
     * @return $this
     */
    public function addPainting(Painting $painting): self
    {
        if (!$this->paintings->contains($painting)) {
            $this->paintings->add($painting);
            $painting->setCategories($this);
        }

        return $this;
    }

    /**
     * @param Painting $painting
     * @return $this
     */
    public function removePainting(Painting $painting): self
    {
        if ($this->paintings->removeElement($painting)) {
            // set the owning side to null (unless already changed)
            if ($painting->getCategories() === $this) {
                $painting->setCategories(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tutoriel>
     */
    public function getTutoriels(): Collection
    {
        return $this->tutoriels;
    }

    public function addTutoriel(Tutoriel $tutoriel): self
    {
        if (!$this->tutoriels->contains($tutoriel)) {
            $this->tutoriels->add($tutoriel);
            $tutoriel->setCategory($this);
        }

        return $this;
    }

    public function removeTutoriel(Tutoriel $tutoriel): self
    {
        if ($this->tutoriels->removeElement($tutoriel)) {
            // set the owning side to null (unless already changed)
            if ($tutoriel->getCategory() === $this) {
                $tutoriel->setCategory(null);
            }
        }

        return $this;
    }
}
