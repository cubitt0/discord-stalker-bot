<?php

declare(strict_types=1);

namespace App\Doctrine\ORM\Traits;

use App\Entity\StalkSettings;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

trait TimestampTrait
{
    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    protected ?DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    protected ?DateTimeInterface $updatedAt;

    /**
     * @param DateTimeInterface $createdAt
     * @return StalkSettings|TimestampTrait
     */
    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeInterface $updatedAt
     * @return StalkSettings|TimestampTrait
     */
    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    public function setTimestampFields(): void
    {
        $this
            ->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime());
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtField(): void
    {
        $this->setUpdatedAt(new DateTime());
    }
}