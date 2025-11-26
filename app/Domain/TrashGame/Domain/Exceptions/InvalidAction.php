<?php

namespace App\Domain\TrashGame\Domain\Exceptions;

use Throwable;

class InvalidAction extends \DomainException
{
    public function __construct(
        string $message = 'Invalid Action',
        int $code = 1001,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
