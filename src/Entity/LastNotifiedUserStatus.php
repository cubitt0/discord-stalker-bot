<?php

namespace App\Entity;

use App\Doctrine\ORM\Interfaces\TimestampInterface;
use App\Doctrine\ORM\Traits\TimestampTrait;
use App\Enum\UserStatusEnum;
use App\Repository\LastNotifiedUserStatusRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LastNotifiedUserStatusRepository::class)]
#[ORM\HasLifecycleCallbacks]
class LastNotifiedUserStatus implements TimestampInterface
{
    use TimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $userid = null;

    #[ORM\Column(length: 255, enumType: UserStatusEnum::class)]
    private ?UserStatusEnum $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserid(): ?string
    {
        return $this->userid;
    }

    public function setUserid(string $userid): static
    {
        $this->userid = $userid;

        return $this;
    }

    public function getStatus(): ?UserStatusEnum
    {
        return $this->status;
    }

    public function setStatus(UserStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }
}
