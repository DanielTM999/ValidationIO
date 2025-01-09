<?php

    namespace Daniel\Validator\Exceptions;

    use Exception;

    class ValidationException extends Exception
    {
        public function __construct(string $message = "Argumento invalido", int $code = 422)
        {
            parent::__construct($message, $code);
        }
    }
?>