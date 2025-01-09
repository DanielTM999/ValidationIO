<?php

    namespace Daniel\Validator;

    interface BaseValidator
    {
        public function isValid($value): bool;
    }
    
?>