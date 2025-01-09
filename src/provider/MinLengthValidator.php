<?php

    namespace Daniel\Validator\Provider;

    use Daniel\Validator\BaseValidator;

    final class MinLengthValidator implements BaseValidator
    {
        private int $minLength;

        public function __construct(int $minLength)
        {
            $this->minLength = $minLength;
        }

        public function isValid($value): bool
        {
            return strlen($value) >= $this->minLength;
        }
    }

?>
