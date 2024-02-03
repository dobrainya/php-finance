<?php

namespace App\Model\Request;

use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class AmountCreateRequest
{
    #[NotBlank]
    private string $name;

    #[NotBlank]
    #[Type('float')]
    private float $amount;

    #[NotBlank]
    #[Choice(['exp', 'inc'])]
    private string $type;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }
}
