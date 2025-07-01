<?php

    namespace Daniel\Validator\constraints;

    use Attribute;
    use Daniel\Validator\Props\AbstractValidation;
    use Daniel\Validator\Props\ValidationProvider;
    use Daniel\Validator\Provider\LengthValidator;

    #[Attribute(Attribute::TARGET_PROPERTY)]
    #[ValidationProvider(new LengthValidator(LengthValidator::TYPE_MAX))]
    final class MaxLength extends AbstractValidation
    {
        public function __construct(int $maxValue = 5, string $message = "")
        {
            if (empty($message)) {
                $message = "O valor excedeu o limite máximo de caracteres permitido: $maxValue";
            }
            parent::__construct($message);
        }
    }
?>