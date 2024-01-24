<?php

namespace App\Model;

class AmountItemResponse
{
    public function __construct(
        private readonly AmountItem $item
    ) {
    }

    public function getItem(): AmountItem
    {
        return $this->item;
    }
}
