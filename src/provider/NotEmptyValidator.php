<?php

    namespace Daniel\Validator\Provider;

    use Daniel\Validator\BaseValidator;

    final class NotEmptyValidator implements BaseValidator
    {
        public function isValid($value): bool{
            return $value !== null && !empty($value);
        }
    }

?>