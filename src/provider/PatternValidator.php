<?php

    namespace Daniel\Validator\Provider;

    use Daniel\Validator\BaseValidator;

    final class PatternValidator extends BaseValidator
    {
        public function isValid($value): bool{
            if ($value === null || $value === '') return true;
            $pattern = $this->getParamArgs()[0] ?? null;
            if ($pattern === null) return false;

            return preg_match($pattern, $value) === 1;
        }
    }

?>