<?php

namespace Daniel\Validator\constraints;

use Attribute;
use Daniel\Validator\Props\AbstractValidation;
use Daniel\Validator\Props\ValidationProvider;
use Daniel\Validator\Provider\EmailValidator;

#[Attribute(Attribute::TARGET_PROPERTY)]
#[ValidationProvider(new EmailValidator())]
final class Email extends AbstractValidation
{
    public function __construct(string $message = "E-mail inválido")
    {
        parent::__construct($message);
    }
}
