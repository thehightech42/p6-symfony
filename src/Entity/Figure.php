<?php

namespace App\Entity;

use App\Repository\FigureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=FigureRepository::class)
 * @UniqueEntity("title", message = "Le titre de la figure dois être unique")
 */
class Figure
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="text")
     */
    private $shortDescription;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updateAt;

    /**
     * @ORM\ManyToOne(targetEntity=GroupeFigure::class, inversedBy="figures")
     * @ORM\JoinColumn(nullable=false)
     */
    private $groupe;

    /**
     * @ORM\OneToMany(targetEntity=VisuelFigure::class, mappedBy="figure", orphanRemoval=true)
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $visuelFigures;

    /**
     * @ORM\OneToOne(targetEntity=VisuelFigure::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $mainVisuel;

    public $mainVisuel2;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="figure", orphanRemoval=true)
     */
    private $comments;

    public function __construct()
    {
        $this->visuelFigures = new ArrayCollection();
        $this->comments = new ArrayCollection();
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
        $this->setSlug($title);

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $this->convertTitleToSlug($slug);

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

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

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeInterface $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->updateAt;
    }

    public function setUpdateAt(\DateTimeInterface $updateAt): self
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    public function getGroupe(): ?GroupeFigure
    {
        return $this->groupe;
    }

    public function setGroupe(?GroupeFigure $groupe): self
    {
        $this->groupe = $groupe;

        return $this;
    }

    public function convertTitleToSlug($title)
    {

        $slug = strtolower(str_replace(" ", "_", $title));

        return $slug;
    }

    /**
     * @return Collection|VisuelFigure[]
     */
    public function getVisuelFigures(): Collection
    {
        return $this->visuelFigures;
    }

    public function addVisuelFigure(VisuelFigure $visuelFigure): self
    {
        if (!$this->visuelFigures->contains($visuelFigure)) {
            $this->visuelFigures[] = $visuelFigure;
            $visuelFigure->setFigure($this);
        }

        return $this;
    }

    public function removeVisuelFigure(VisuelFigure $visuelFigure): self
    {
        if ($this->visuelFigures->removeElement($visuelFigure)) {
            // set the owning side to null (unless already changed)
            if ($visuelFigure->getFigure() === $this) {
                $visuelFigure->setFigure(null);
            }
        }

        return $this;
    }

    public function getMainVisuel(): ?VisuelFigure
    {
        return $this->mainVisuel;
    }

    public function setMainVisuel(?VisuelFigure $mainVisuel): self
    {
        $this->mainVisuel = $mainVisuel;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setFigure($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getFigure() === $this) {
                $comment->setFigure(null);
            }
        }

        return $this;
    }

}
