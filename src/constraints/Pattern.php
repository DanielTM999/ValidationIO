<?php

    namespace Daniel\Validator\constraints;

    use Attribute;
    use Daniel\Validator\Props\AbstractValidation;
    use Daniel\Validator\Props\ValidationProvider;
    use Daniel\Validator\Provider\PatternValidator;


    #[Attribute(Attribute::TARGET_PROPERTY)]
    #[ValidationProvider(new PatternValidator())]
    final class Pattern extends AbstractValidation
    {
        public function __construct(string $message = "O valor informado não corresponde ao esperado.")
        {
            parent::__construct($message);
        }
    }
    
?>