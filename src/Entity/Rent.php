<?php

namespace App\Entity;

use App\Repository\RentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RentRepository::class)]
class Rent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Movie::class, inversedBy: 'rents')]
    #[ORM\JoinColumn(nullable: false)]
    private $movie;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'rents')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: 'string', length: 255)]
    private $token;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private $startsAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private $endsAt;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $guestEmail;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

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

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

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

    public function getStartsAt(): ?\DateTimeImmutable
    {
        return $this->startsAt;
    }

    public function setStartsAt(\DateTimeImmutable $startsAt): self
    {
        $this->startsAt = $startsAt;

        return $this;
    }

    public function getEndsAt(): ?\DateTimeImmutable
    {
        return $this->endsAt;
    }

    public function setEndsAt(\DateTimeImmutable $endsAt): self
    {
        $this->endsAt = $endsAt;

        return $this;
    }

    public function getGuestEmail(): ?string
    {
        return $this->guestEmail;
    }

    public function setGuestEmail(?string $guestEmail): self
    {
        $this->guestEmail = $guestEmail;

        return $this;
    }
}
