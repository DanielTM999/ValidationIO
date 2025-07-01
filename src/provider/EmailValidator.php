<?php

    namespace Daniel\Validator\Provider;

    use Daniel\Validator\BaseValidator;

    final class EmailValidator extends BaseValidator
    {
        public function isValid($value): bool
        {
            return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
        }
    }

?>
