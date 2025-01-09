<?php

    namespace Daniel\Validator\Props;

    use Attribute;

    #[Attribute(Attribute::TARGET_CLASS|Attribute::TARGET_CLASS_CONSTANT)]
    final class NullableValidation
    {
        public function __construct(public bool $enable = false) {

        }
    }
    

?>