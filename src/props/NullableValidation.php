<?php

    namespace Daniel\Validator\Props;

    use Attribute;

    #[Attribute(Attribute::TARGET_PROPERTY)]
    final class NullableValidation
    {
        public function __construct(public bool $enable = false) {

        }
    }
    

?>