<?php

namespace App\Model;

use App\Entity\Amount;

class AmountItem
{
    public function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly float $amount,
        private readonly \DateTimeInterface $createdAt,
        private readonly string $type
    ) {
    }

    public static function createFromEntity(Amount $amount): self
    {
        return new self(
            $amount->getId(),
            $amount->getName(),
            $amount->getAmount(),
            $amount->getCreatedAt(),
            $amount->getType()->getCode()
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
