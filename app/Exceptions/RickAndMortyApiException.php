<?php

namespace App\Exceptions;

use Exception;

class RickAndMortyApiException extends Exception
{
    public function __construct(
        string $message,
        private readonly int $statusCode = 500,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}