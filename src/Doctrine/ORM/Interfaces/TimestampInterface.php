<?php

declare(strict_types=1);

namespace App\Doctrine\ORM\Interfaces;

use DateTimeInterface;

interface TimestampInterface
{
    /**
     * @param DateTimeInterface $createdAt
     * @return TimestampInterface
     */
    public function setCreatedAt(DateTimeInterface $createdAt): self;

    public function getCreatedAt(): ?DateTimeInterface;

    /**
     * @param DateTimeInterface $updatedAt
     * @return TimestampInterface
     */
    public function setUpdatedAt(DateTimeInterface $updatedAt): self;

    public function getUpdatedAt(): ?DateTimeInterface;
}