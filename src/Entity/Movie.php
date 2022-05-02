<?php

namespace App\Entity;

use App\Repository\MovieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: MovieRepository::class)]
#[UniqueEntity('title', message: 'Le titre {{ value }} est déjà utilisé')]
class Movie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private $synopsis;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private $releasedAt;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $image;

    #[ORM\OneToMany(mappedBy: 'movie', targetEntity: Mention::class, orphanRemoval: true)]
    private $mentions;

    #[ORM\OneToMany(mappedBy: 'movie', targetEntity: Comment::class, orphanRemoval: true)]
    private $comments;

    #[ORM\OneToMany(mappedBy: 'movie', targetEntity: Rent::class, orphanRemoval: true)]
    private $rents;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->mentions = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->rents = new ArrayCollection();
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

    public function getSynopsis(): ?string
    {
        return $this->synopsis;
    }

    public function setSynopsis(?string $synopsis): self
    {
        $this->synopsis = $synopsis;

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

    public function getReleasedAt(): ?\DateTimeImmutable
    {
        return $this->releasedAt;
    }

    public function setReleasedAt(\DateTimeImmutable $releasedAt): self
    {
        $this->releasedAt = $releasedAt;

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

    /**
     * @return Collection<int, Mention>
     */
    public function getMentions(): Collection
    {
        return $this->mentions;
    }

    public function addMention(Mention $mention): self
    {
        if (!$this->mentions->contains($mention)) {
            $this->mentions[] = $mention;
            $mention->setMovie($this);
        }

        return $this;
    }

    public function removeMention(Mention $mention): self
    {
        if ($this->mentions->removeElement($mention)) {
            // set the owning side to null (unless already changed)
            if ($mention->getMovie() === $this) {
                $mention->setMovie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setMovie($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getMovie() === $this) {
                $comment->setMovie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Rent>
     */
    public function getRents(): Collection
    {
        return $this->rents;
    }

    public function addRent(Rent $rent): self
    {
        if (!$this->rents->contains($rent)) {
            $this->rents[] = $rent;
            $rent->setMovie($this);
        }

        return $this;
    }

    public function removeRent(Rent $rent): self
    {
        if ($this->rents->removeElement($rent)) {
            // set the owning side to null (unless already changed)
            if ($rent->getMovie() === $this) {
                $rent->setMovie(null);
            }
        }

        return $this;
    }
}
