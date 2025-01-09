# Validador para o Framework Origins

Este projeto implementa um sistema de validação para o framework **Origins**, permitindo que os usuários definam e utilizem regras de validação personalizadas.

## 📌 Funcionalidades
- Validações baseadas em atributos (`#[Attribute]`).
- Suporte a validações nativas (`NotEmpty`, `Email`, etc.).
- Possibilidade de criação de validações personalizadas.
- Integração com controllers do **Origins** através de `#[Valid]`.

## 📦 Instalação

Este validador depende do **framework Origins**. Certifique-se de que ele já está instalado antes de continuar.

1. Adicione o validador ao seu projeto utilizando o Composer:
   ```sh
   composer require daniel/origins-validator
   ```

## 🚀 Como Usar
### 1️⃣ Criando um Modelo com Validações
```php
use Daniel\Validator\Valid\Email;
use Daniel\Validator\Valid\NotEmpty;

final class ModelValidation
{
    #[NotEmpty]
    private string $nome;

    #[Email]
    private string $email;
}
```

### 2️⃣ Criando um Controller com Validação
```php
use Daniel\Origins\Controller;
use Daniel\Origins\Get;
use Daniel\Origins\Request;
use Daniel\Validator\Props\Valid;

#[Controller]
final class TesteController
{
    #[Get("/")]
    #[Valid(ModelValidation::class)]
    function index(Request $request) {
        // A validação será processada automaticamente
    }
}
```

## ✨ Criando uma Validação Personalizada
1. Crie um atributo que estenda `AbstractValidation`:
   ```php
   use Daniel\Validator\Props\AbstractValidation;
   use Attribute;

   #[Attribute(Attribute::TARGET_PROPERTY)]
   class MinLength extends AbstractValidation
   {
       public function __construct(private int $length, string $message = "Valor muito curto.") {
           parent::__construct($message);
       }
   }
   ```
2. Crie um provedor que implemente `BaseValidator`:
   ```php
   use Daniel\Validator\BaseValidator;

   class MinLengthValidator implements BaseValidator
   {
       private int $length;

       public function __construct(int $length)
       {
           $this->length = $length;
       }

       public function isValid($value): bool
       {
           return strlen($value) >= $this->length;
       }
   }
   ```
3. Agora você pode usar `#[MinLength(5)]` em suas propriedades!

## 📜 Licença
Este projeto é distribuído sob a licença MIT.

---

Para mais informações, acesse [Origins Framework](https://github.com/DanielTM999/origins). 🚀

