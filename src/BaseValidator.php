<?php

    namespace Daniel\Validator;

    abstract class BaseValidator
    {
        private array $paramArgs;

        public function __construct(array $paramArgs = []){
            $this->paramArgs = $paramArgs;
        }
        public abstract function isValid($value): bool;
        public function getParamArgs(): array { return $this->paramArgs; }
        public function setParamArgs(array $paramArgs): self { $this->paramArgs = $paramArgs; return $this; }
    }
    
?>