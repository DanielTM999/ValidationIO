<?php

    namespace Daniel\Validator\Provider;

    use Daniel\Validator\BaseValidator;

    final class CPFValidator extends BaseValidator
    {
        public function isValid($value): bool{
            if($value === null)  return true;
            $cpf = preg_replace('/[^0-9]/', '', $value);
            if (strlen($cpf) !== 11) return false;
            if (preg_match('/^(\d)\1{10}$/', $cpf)) return false;
            for ($t = 9; $t < 11; $t++) {
                $sum = 0;
                for ($i = 0; $i < $t; $i++) {
                    $sum += $cpf[$i] * (($t + 1) - $i);
                }
                $remainder = ($sum * 10) % 11;
                if ($remainder == 10) $remainder = 0;
                if ($cpf[$t] != $remainder) return false;
            }
            return true;
        }
    }

?>