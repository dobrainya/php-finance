<?php

namespace App\Model;

class ReferenceItemResponse
{
    public function __construct(
        private readonly ReferenceItem $item
    ) {
    }

    public function getItem(): ReferenceItem
    {
        return $this->item;
    }
}
