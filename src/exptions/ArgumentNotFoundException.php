<?php

    namespace Daniel\Validator\Exceptions;

    use Exception;

    class ArgumentNotFoundException extends Exception
    {
        public function __construct(string $message = "Argumento não encontrado", int $code = 422)
        {
            parent::__construct($message, $code);
        }
    }
?>