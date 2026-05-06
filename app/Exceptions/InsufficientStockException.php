<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    public array $insufficient;

    public function __construct(array $insufficient, string $message = 'Stok bahan tidak cukup')
    {
        $this->insufficient = $insufficient;
        parent::__construct($message.': '.implode(', ', $insufficient));
    }
}
