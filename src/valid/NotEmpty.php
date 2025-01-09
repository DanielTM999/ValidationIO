<?php

    namespace Daniel\Validator\Valid;

    use Attribute;
    use Daniel\Validator\Props\AbstractValidation;
    use Daniel\Validator\Props\ValidationProvider;
    use Daniel\Validator\Provider\NotEmptyValidator;


    #[Attribute(Attribute::TARGET_PROPERTY)]
    #[ValidationProvider(new NotEmptyValidator())]
    final class NotEmpty extends AbstractValidation
    {
        public function __construct(string $message = "Valor não pode ser vazio")
        {
            parent::__construct($message);
        }
    }
    
?>