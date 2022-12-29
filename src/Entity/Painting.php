<?php

namespace App\Entity;




use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Repository\PaintingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PaintingRepository::class)]
#[Vich\Uploadable]
#[UniqueEntity(
    fields: ['title'],
    message: 'ce titre existe déja'
)]
class Painting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Length(
        max:30,
        maxMessage:'Votre titre peut contenir au max{{ limit }} characters ',
    )]
    #[ORM\Column(length: 120)]
    private ?string $title = null;

    #[Assert\Length(
        min:10,
        minMessage:'Votre contenu doit contenir au moins{{ limit }} characters ',
    )]
    #[ORM\Column(length: 255)]
    private ?string $descrioption = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;


    #[Assert\Range(
        notInRangeMessage: 'Votre tableau ne doit être compris entre {{ min }} et {{ max }} cm de hauteur',
        min: 20,
        max: 114
    )]
    #[ORM\Column]
    private ?float $height = null;
    #[Assert\Range(
        notInRangeMessage: 'Votre tableau ne doit être compris entre {{ min }} et {{ max }} cm de largeur',
        min: 25,
        max: 195
    )]
    #[ORM\Column]
    private ?float $width = null;

    #[ORM\Column(type: 'string')]
    private ?string $imageName = null;


    #[Vich\UploadableField(mapping: 'Oeuvre_image', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;


    #[ORM\ManyToOne(inversedBy: 'paintings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Style $style = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'painting', targetEntity: Comment::class)]
    private Collection $comment;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\ManyToOne(inversedBy: 'paintings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $categories = null;

    #[ORM\OneToMany(mappedBy: 'paintlike', targetEntity: Like::class)]
    private Collection $likes;

    #[ORM\ManyToOne(inversedBy: 'paintings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $vendeur = null;


    public function __construct()
    {
        $this->comment = new ArrayCollection();
        $this->likes = new ArrayCollection();

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

    public function getDescrioption(): ?string
    {
        return $this->descrioption;
    }

    public function setDescrioption(string $descrioption): self
    {
        $this->descrioption = $descrioption;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(float $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getWidth(): ?float
    {
        return $this->width;
    }

    public function setWidth(float $width): self
    {
        $this->width = $width;

        return $this;
    }


    public function getImageName(): ?string
    {
        return $this->imageName;
    }


    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;
        return $this;
    }


    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {

            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }


    public function getStyle(): ?Style
    {
        return $this->style;
    }

    public function setStyle(?Style $style): self
    {
        $this->style = $style;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function createSlug()
    {
        $slugify = new Slugify();
        $this->slug = $slugify->slugify($this->title);

    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComment(): Collection
    {
        return $this->comment;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comment->contains($comment)) {
            $this->comment->add($comment);
            $comment->setPainting($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comment->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPainting() === $this) {
                $comment->setPainting(null);
            }
        }

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCategories(): ?Category
    {
        return $this->categories;
    }

    public function setCategories(?Category $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setPaintlike($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getPaintlike() === $this) {
                $like->setPaintlike(null);
            }
        }

        return $this;
    }

    public function getVendeur(): ?User
    {
        return $this->vendeur;
    }

    public function setVendeur(?User $vendeur): self
    {
        $this->vendeur = $vendeur;

        return $this;
    }



}
