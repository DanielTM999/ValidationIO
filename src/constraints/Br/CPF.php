<?php

namespace Daniel\Validator\constraints\Br;

use Attribute;
use Daniel\Validator\Props\AbstractValidation;
use Daniel\Validator\Props\ValidationProvider;
use Daniel\Validator\Provider\CPFValidator;

#[Attribute(Attribute::TARGET_PROPERTY)]
#[ValidationProvider(new CPFValidator())]
final class CPF extends AbstractValidation
{
    public function __construct(string $message = "Cadastro de pessoa fisica inválido")
    {
        parent::__construct($message);
    }
}
