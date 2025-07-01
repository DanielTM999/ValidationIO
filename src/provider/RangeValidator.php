<?php

    namespace Daniel\Validator\Provider;

    use Daniel\Validator\BaseValidator;

    final class RangeValidator extends BaseValidator
    {
        public function isValid($value): bool
        {
            if (!is_numeric($value)) {
                return false;
            }
            $min = $this->getbaseMinSize();
            $max = $this->getbaseMaxSize();

            return $value >= $min && $value <= $max;
        }

        private function getbaseMinSize(): int{
            return $this->getParamArgs()[0] ?? 0;
        }

        private function getbaseMaxSize(): int{
            return $this->getParamArgs()[1] ?? 100;
        }

    }

?>
