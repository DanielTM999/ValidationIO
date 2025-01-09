<?php

    namespace Daniel\Validator\Exceptions;

    use Exception;

    class ValidationMethodNotAllowedException extends Exception
    {
        public function __construct(string $message = "Método não permitido para validação", int $code = 422)
        {
            parent::__construct($message, $code);
        }
    }
?>