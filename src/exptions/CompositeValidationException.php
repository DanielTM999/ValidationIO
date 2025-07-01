<?php

namespace Daniel\Validator\Exceptions;

use Daniel\Origins\Exceptions\CompositeException;
use Exception;

class CompositeValidationException extends CompositeException
{
    public function __construct(array $errors = [], string $message = "", int $code = 422)
    {
        if (empty($message)) {
            $message = $this->extractMessageFromErrors($errors);
        }
        parent::__construct($errors, $message, $code);
    }
}
