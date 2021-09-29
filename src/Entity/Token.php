<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TokenRepository;

/**
 * @ORM\Entity(repositoryClass=TokenRepository::class)
 */
class Token
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $hash;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createAt;

    public function __construct(User $user)
    {
        $this->setUserObjInToken($user);
        $this->setCreateAt(new \DateTime);
        $this->setHash();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserObjInToken(): ?User
    {
        return $this->user;
    }

    public function setUserObjInToken(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(): self
    {
        $hash = hash('sha256', time());
        $this->hash = $hash;

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
}
