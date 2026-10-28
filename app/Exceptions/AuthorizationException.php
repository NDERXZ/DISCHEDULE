<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class AuthorizationException extends Exception
{
    public function __construct(string $message = "This action is unauthorized.", int $code = 403)
    {
        parent::__construct($message, $code);
    }
}
