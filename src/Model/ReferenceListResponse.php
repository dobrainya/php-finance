<?php

namespace App\Model;

class ReferenceListResponse
{
    /**
     * @param ReferenceItem[] $items
     */
    public function __construct(private readonly array $items = [])
    {
    }

    /**
     * @return ReferenceItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
