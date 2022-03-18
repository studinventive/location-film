<?php

namespace App\Entity;

use App\Repository\MentionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MentionRepository::class)]
class Mention
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Movie::class, inversedBy: 'mentions')]
    #[ORM\JoinColumn(nullable: false)]
    private $movie;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'mentions')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: 'boolean', nullable: true)]
    /** @var bool $isLike Defines wether the mention is a like or not */
    private $isLike = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMovie(): ?Movie
    {
        return $this->movie;
    }

    public function setMovie(?Movie $movie): self
    {
        $this->movie = $movie;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getIsLike(): bool
    {
        return $this->isLike ? true : false;
    }

    public function setIsLike(?bool $isLike): self
    {
        $this->isLike = $isLike ? true : false;

        return $this;
    }
}
