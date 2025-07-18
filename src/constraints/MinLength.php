<?php

    namespace Daniel\Validator\constraints;

    use Attribute;
    use Daniel\Validator\Props\AbstractValidation;
    use Daniel\Validator\Props\ValidationProvider;
    use Daniel\Validator\Provider\LengthValidator;

    #[Attribute(Attribute::TARGET_PROPERTY)]
    #[ValidationProvider(new LengthValidator(LengthValidator::TYPE_MIN))]
    final class MinLength extends AbstractValidation
    {
        public function __construct(int $minValue = 1, string $message = "")
        {
            if (empty($message)) {
                $message = "O valor é menor que o limite mínimo de caracteres permitido: $minValue";
            }
            parent::__construct($message);
        }
    }
?>