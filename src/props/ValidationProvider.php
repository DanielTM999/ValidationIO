<?php

    namespace Daniel\Validator\Props;

    use Attribute;
    use Daniel\Validator\BaseValidator;

    #[Attribute(Attribute::TARGET_CLASS)]
    final class ValidationProvider
    {

        public function __construct(public BaseValidator $classProvider) {
            if (empty($class)) {
                throw new \InvalidArgumentException('A classe(provedora) não pode ser vazia.');
            }
        }
    }
    
?>