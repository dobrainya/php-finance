<?php

namespace App\Model;

class AmountListResponse
{
    /**
     * @param AmountItem[] $items
     */
    public function __construct(private readonly array $items = [])
    {
    }

    /**
     * @return AmountItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
