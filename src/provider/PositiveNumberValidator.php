<?php

    namespace Daniel\Validator\Provider;

    use Daniel\Validator\BaseValidator;

    final class PositiveNumberValidator extends BaseValidator
    {
        private bool $allowZero;

        public function __construct(bool $allowZero = true) {
            $this->allowZero = $allowZero;
        }

        public function isValid($value): bool
        {
            if($value === null) return true;
            if (!is_numeric($value)) {
                return false;
            }

            if($this->allowZero){
                return $value >= 0;
            }
            return $value > 0;
        }
    }

?>
