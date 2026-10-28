<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class AuthenticationException extends Exception
{
    public function __construct(string $message = "Unauthenticated.", int $code = 401)
    {
        parent::__construct($message, $code);
    }
}
