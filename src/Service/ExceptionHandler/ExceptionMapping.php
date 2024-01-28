<?php

namespace App\Service\ExceptionHandler;

class ExceptionMapping
{
    public function __construct(
        private readonly int $code,
        private readonly bool $hidden,
        private readonly bool $loggable
    ) {
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function isLoggable(): bool
    {
        return $this->loggable;
    }
}
