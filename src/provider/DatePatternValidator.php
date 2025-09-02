<?php

    namespace Daniel\Validator\Provider;

    use Daniel\Validator\BaseValidator;

    final class DatePatternValidator extends BaseValidator
    {
        public function isValid($value): bool{
            if ($value === null || $value === '') return true;
            $formats = $this->getParamArgs()[0] ?? null;
            if ($formats === null) return false;

            if (is_string($formats)) {
                return $this->validateFormat($value, $formats);
            }

            if (is_array($formats)) {
                foreach ($formats as $format) {
                    if (is_string($format) && $this->validateFormat($value, $format)) {
                        return true;
                    }
                }
                return false;
            }

            return false;
        }

        private function validateFormat(string $value, string $format): bool
        {
            $dt = \DateTime::createFromFormat($format, $value);
            $errors = \DateTime::getLastErrors();

            if ($dt === false) return false;
            if ($errors === false) return true;
            return $dt !== false && $errors['warning_count'] === 0 && $errors['error_count'] === 0;
        }
    }

?>