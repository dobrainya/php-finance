<?php

namespace App\Model;

use App\Entity\Reference;

class ReferenceItem
{
    public function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly string $code
    ) {
    }

    public static function createFromEntity(Reference $reference): self
    {
        return new self(
            $reference->getId(),
            $reference->getName(),
            $reference->getCode(),
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

    public function getCode(): string
    {
        return $this->code;
    }
}
