<?php

namespace App\Entity;

use App\Repository\SliderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SliderRepository::class)]
class Slider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: Painting::class, inversedBy: 'sliders')]
    private Collection $picture;

    public function __construct()
    {
        $this->picture = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Painting>
     */
    public function getPicture(): Collection
    {
        return $this->picture;
    }

    public function addPicture(Painting $picture): self
    {
        if (!$this->picture->contains($picture)) {
            $this->picture->add($picture);
        }

        return $this;
    }

    public function removePicture(Painting $picture): self
    {
        $this->picture->removeElement($picture);

        return $this;
    }
}
