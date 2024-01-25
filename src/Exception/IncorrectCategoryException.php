<?php

namespace App\Exception;

class IncorrectCategoryException extends \RuntimeException
{
    public function __construct($message = 'Got incorrect category', $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
