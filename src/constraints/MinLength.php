<?php

    namespace Daniel\Validator\constraints;

    use Attribute;
    use Daniel\Validator\Props\AbstractValidation;
    use Daniel\Validator\Props\ValidationProvider;
    use Daniel\Validator\Provider\MinLengthValidator;

    #[Attribute(Attribute::TARGET_PROPERTY)]
    #[ValidationProvider(new MinLengthValidator(6))]
    final class MinLength extends AbstractValidation
    {
        public function __construct(string $message = "O valor deve ter pelo menos 6 caracteres")
        {
            parent::__construct($message);
        }
    }
?>