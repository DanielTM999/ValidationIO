<?php

    namespace Daniel\Validator\Props;

    use Attribute;

    #[Attribute(Attribute::TARGET_PROPERTY)]
    class AbstractValidation
    {
        protected string $message;

        public function __construct(string $message = "Valor inválido.")
        {
            $this->message = $message;
        }

        public function getMessage(): string{
            return $this->message;
        }
    }
?>
    