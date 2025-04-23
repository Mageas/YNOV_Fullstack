<?php

namespace App\Traits;

use App\Enums\Status;
use App\Exceptions\StatusNotFoundException;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait StatisticsPropertiesTrait
{
    #[ORM\Column]
    #[Groups(['stats'])]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['stats'])]
    private ?DateTimeInterface $updatedAt = null;

    #[ORM\Column(length: 10)]
    #[Groups(['stats'])]
    private ?string $status = null;

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        if (Status::exists($status)) {
            $this->status = $status;

            return $this;
        }

        throw new StatusNotFoundException();
    }

    #[ORM\PrePersist]
    public function initializeTimestamps(): void
    {
        $this->setCreatedAt(new DateTimeImmutable());
        $this->setUpdatedAt(new DateTime());
    }

    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->setUpdatedAt(new DateTime());
    }
}
