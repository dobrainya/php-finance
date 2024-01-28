<?php

namespace App\Exception;

class CategoryNotFoundException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Category not found');
    }
}
