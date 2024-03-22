<?php

namespace App\Entity;

use App\Doctrine\ORM\Interfaces\TimestampInterface;
use App\Doctrine\ORM\Traits\TimestampTrait;
use App\Repository\StalkSettingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StalkSettingsRepository::class)]
#[ORM\UniqueConstraint('stalker_stalked_idx',['stalker','stalked'])]
#[ORM\HasLifecycleCallbacks]
class StalkSettings implements TimestampInterface
{
    use TimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $stalker = null;

    #[ORM\Column(length: 255)]
    private ?string $stalked = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStalker(): ?string
    {
        return $this->stalker;
    }

    public function setStalker(string $stalker): static
    {
        $this->stalker = $stalker;

        return $this;
    }

    public function getStalked(): ?string
    {
        return $this->stalked;
    }

    public function setStalked(string $stalked): static
    {
        $this->stalked = $stalked;

        return $this;
    }
}
