<?php

    namespace Daniel\Validator\constraints;

    use Attribute;
    use Daniel\Validator\Props\AbstractValidation;
    use Daniel\Validator\Props\ValidationProvider;
    use Daniel\Validator\Provider\RangeValidator;

    #[Attribute(Attribute::TARGET_PROPERTY)]
    #[ValidationProvider(new RangeValidator())]
    final class Range extends AbstractValidation
    {
        public function __construct(int $minValue = 0, int $maxValue = 100, string $message = "")
        {
            if (empty($message)) {
                $message = "O valor deve estar entre $minValue e $maxValue.";
            }
            parent::__construct($message);
        }
    }
?>