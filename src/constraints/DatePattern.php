<?php

    namespace Daniel\Validator\constraints;

    use Attribute;
    use Daniel\Validator\Props\AbstractValidation;
    use Daniel\Validator\Props\ValidationProvider;
    use Daniel\Validator\Provider\DatePatternValidator;


    #[Attribute(Attribute::TARGET_PROPERTY)]
    #[ValidationProvider(new DatePatternValidator())]
    final class DatePattern extends AbstractValidation
    {
        public const YYYY_MM_DD = 'Y-m-d';
        public const DD_MM_YYYY = 'd/m/Y';
        public const YYYY_MM_DD_HH_MM = 'Y-m-d H:i';

        public function __construct(array|string $patterns, string $message = "O valor informado não corresponde ao esperado.")
        {
            parent::__construct($message);
        }
    }
    
?>