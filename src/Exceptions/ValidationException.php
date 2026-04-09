<?php 
namespace Authentica\LaravelAuthentica\Exceptions;

class AuthenticaException extends \Exception {}

namespace Authentica\LaravelAuthentica\Exceptions;

class ValidationException extends AuthenticaException
{
    public function __construct(string $message = "", protected mixed $errors = null)
    {
        parent::__construct($message, 422);
    }

    public function getErrors(): mixed
    {
        return $this->errors;
    }
}