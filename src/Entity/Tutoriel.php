<?php

namespace App\Entity;

use App\Repository\TutorielRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TutorielRepository::class)]
class Tutoriel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'tutoriels')]
    private ?Category $Category = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: 'string')]
    private ?string $image = null;

    #[ORM\OneToMany(mappedBy: 'tutorial_id', targetEntity: TutoComment::class)]
    private Collection $comment_id;

    #[ORM\ManyToOne(inversedBy: 'tutoriels')]
    private ?Style $style_id = null;

    public function __construct()
    {
        $this->comment_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
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


    public function getCategory(): ?Category
    {
        return $this->Category;
    }

    public function setCategory(?Category $Category): self
    {
        $this->Category = $Category;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, TutoComment>
     */
    public function getCommentId(): Collection
    {
        return $this->comment_id;
    }

    public function addUserId(TutoComment $commentId): self
    {
        if (!$this->comment_id->contains($commentId)) {
            $this->comment_id->add($commentId);
            $commentId->setTutorialId($this);
        }

        return $this;
    }

    public function removeUserId(TutoComment $commentId): self
    {
        if ($this->comment_id->removeElement($commentId)) {
            // set the owning side to null (unless already changed)
            if ($commentId->getTutorialId() === $this) {
                $commentId->setTutorialId(null);
            }
        }

        return $this;
    }

    public function getStyleId(): ?Style
    {
        return $this->style_id;
    }

    public function setStyleId(?Style $style_id): self
    {
        $this->style_id = $style_id;

        return $this;
    }
}
