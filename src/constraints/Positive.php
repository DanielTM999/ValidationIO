<?php

    namespace Daniel\Validator\constraints;

    use Attribute;
    use Daniel\Validator\Props\AbstractValidation;
    use Daniel\Validator\Props\ValidationProvider;
    use Daniel\Validator\Provider\PositiveNumberValidator;

    #[Attribute(Attribute::TARGET_PROPERTY)]
    #[ValidationProvider(new PositiveNumberValidator(false))]
    final class Positive extends AbstractValidation
    {
        public function __construct(string $message = "O valor deve ser maior que 0.")
        {
            if (empty($message)) {
                $message = "O valor deve ser maior que 0.";
            }
            parent::__construct($message);
        }
    }

?>