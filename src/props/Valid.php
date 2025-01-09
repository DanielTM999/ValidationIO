<?php

    namespace Daniel\Validator\Props;

    use Attribute;

    #[Attribute(Attribute::TARGET_METHOD)]
    final class Valid
    {
        public function __construct(public string $class) {
            if (empty($class)) {
                throw new \InvalidArgumentException('A classe(Validadora) não pode ser vazia.');
            }
        }
    }
    
?>