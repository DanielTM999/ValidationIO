<?php

    namespace Daniel\Validator;

    use Daniel\Origins\AnnotationsUtils;
use Daniel\Origins\Log;
use Daniel\Validator\Exceptions\ArgumentNotFoundException;
    use Daniel\Validator\Exceptions\ValidationException;
    use Daniel\Validator\Exceptions\ValidationMethodNotAllowedException;
    use Daniel\Validator\Props\AbstractValidation;
    use Daniel\Validator\constraints\NotNull;
    use Daniel\Validator\Exceptions\CompositeValidationException;
    use Daniel\Validator\Props\ListOf;
    use Daniel\Validator\Props\ProviderExtractor;
    use Daniel\Validator\Props\Valid;
    use ReflectionAttribute;
    use ReflectionClass;
    use ReflectionProperty;

    final class ValidatorManager 
    {

        private array $errors;

        private function __construct(private string $className, private array $obj) {
            $this->className = $className;
            $this->obj = $obj;
            $this->errors = [];
        }

        public static function getValidator(string $className, array $obj): ValidatorManager{
            return new ValidatorManager($className, $obj);
        }

        public function executeValidation(){
            $request = $this->obj;
            $validationClassReference = $this->className;

            if(!isset($request)){
                throw new ValidationMethodNotAllowedException("Método não permitido para validação não possue uma 'Request' no metodo");
            }
            
            $reflectionClassReference = new ReflectionClass($validationClassReference);

            $variables = $reflectionClassReference->getProperties();

            foreach($variables as $var){
                $this->validateField($var, $request);
            }

            if($this->hasErrors()){
                throw new CompositeValidationException($this->errors);
            }
        }

        public function hasErrors(){
            return !empty($this->errors);
        }

        private function validateField(ReflectionProperty $var, array &$reqBody){
            $varName = $var->getName();
            $attributes = $var->getAttributes();
            
            $varType = $var->getType();
            $typeName = ($varType instanceof \ReflectionNamedType) ? $varType->getName() : null;
            $isNullable = ($varType instanceof \ReflectionNamedType) && $varType->allowsNull();
            $isRequired = !$this->isNullValidation($var) && !$isNullable;

            if (!array_key_exists($varName, $reqBody)) {
                if ($isRequired) {
                    $this->errors[] = new ArgumentNotFoundException("Argumento '$varName' não encontrado na requisição");
                }
                return;
            }

            $value = $reqBody[$varName];
            $isArray = $typeName === 'array';
            $isObject = $typeName && class_exists($typeName);
            $applyValidation = !empty($var->getAttributes(Valid::class));
            $isValidatableObject = $isObject && $applyValidation;

            if ($isArray && $applyValidation) {
                $this->applyValidations($attributes, $value);
                $this->validateArray($var, $value);
            }else if ($isValidatableObject){
                $this->validateObject($var, $value);
            }else{
                if(!$isObject){
                    $this->applyValidations($attributes, $value);
                }
            }

        }

        private function validateObject(ReflectionProperty $var, array $req){
            $typeName =  $var->getType()->getName();
            $reflectionClassReference = new ReflectionClass($typeName);

            $variables = $reflectionClassReference->getProperties();
            
            foreach($variables as $var){
                $this->validateField($var, $req);
            }
        }

        private function validateDetachedObject(ReflectionClass $reflectionClassReference, array $req){
            $variables = $reflectionClassReference->getProperties();
            
            foreach($variables as $var){
                $this->validateField( $var, $req);
            }
        }

        private function validateArray(ReflectionProperty $var, array $req){
            $validList = AnnotationsUtils::isAnnotationPresent($var, ListOf::class);
            if($validList){
                $className = AnnotationsUtils::getAnnotationArgs($var, ListOf::class)[0] ?? null;
                if (!$className || !class_exists($className)) {
                    $this->errors[] = new ValidationException("Classe da lista não encontrada ou inválida");
                    return;
                }
                if(class_exists($className)){
                    $reflectionClassReference = new ReflectionClass($className);
                    
                    foreach($req as $toValid){
                        $this->validateDetachedObject($reflectionClassReference, $toValid);
                    }

                   }else{
                    $this->errors[] = new ValidationException("Classe da lista não encontrada ou inválida");
                }
            }
        }

        private function setAtrubuteArgs(BaseValidator &$validator, ReflectionAttribute $attr){
            $args = AnnotationsUtils::getAnnotationArgs($attr, "");
            $validator->setParamArgs($args);
        }

        private function getProviderByAtrubute(ReflectionClass $attr): BaseValidator{
            return ProviderExtractor::getProvider($attr);
        }

        private function getMessageByAtribute(ReflectionAttribute $attr){
            $instance = $attr->newInstance();
            return $instance->getMessage();
        }

        private function isNullValidation(ReflectionProperty $reflectionClassReference): bool{
            return !AnnotationsUtils::isAnnotationPresent($reflectionClassReference, NotNull::class);
        }

        private function applyValidations(array $attributes, mixed $value): void {
            foreach ($attributes as $attribute) {
                $reflection = new ReflectionClass($attribute->getName());
                $parentClass = $reflection->getParentClass();
                $parentClassName = $parentClass ? $parentClass->getName() : null;
                if ($parentClass && $parentClassName === AbstractValidation::class){
                    $provider = $this->getProviderByAtrubute($reflection);
                    $this->setAtrubuteArgs($provider, $attribute);
                    $result = $provider->isValid($value);
                    if(!$result){
                        $msg =  $this->getMessageByAtribute($attribute);
                        $this->errors[] = new ValidationException($msg);
                    }
                }
                unset($reflection);
            }
        }


    }
    

?>