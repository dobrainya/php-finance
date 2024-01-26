<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class CategoryNotFoundException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Category not found', Response::HTTP_NOT_FOUND);
    }
}
