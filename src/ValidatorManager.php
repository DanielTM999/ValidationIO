<?php

    namespace Daniel\Validator;

    use Daniel\Origins\AnnotationsUtils;
    use Daniel\Validator\Exceptions\ArgumentNotFoundException;
    use Daniel\Validator\Exceptions\ValidationException;
    use Daniel\Validator\Exceptions\ValidationMethodNotAllowedException;
    use Daniel\Validator\Props\AbstractValidation;
    use Daniel\Validator\constraints\NotNull;
use Daniel\Validator\Exceptions\CompositeValidationException;
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
            $model = null;
            
            try {
                $model = $reflectionClassReference->newInstance();
            } catch (\Throwable $th) {

            }

            foreach($variables as $var){
                $this->validateField($model, $var, $request);
            }

            if($this->hasErrors()){
                throw new CompositeValidationException($this->errors);
            }

            $indexInject = $injectModel["index"] ?? -1;
            if($indexInject >= 0){
                $varArgs[$indexInject] = $model;
            }

            return $model;
        }

        public function hasErrors(){
            return !empty($this->errors);
        }

        private function validateField(object &$model, ReflectionProperty $var, array &$reqBody){
            $enableNullValidation = $this->isNullValidation($var);
            $varName = $var->getName();
            $atributes = $var->getAttributes();
            if(isset($reqBody[$varName])){
                $value = $reqBody[$varName];
                $varType = $var->getType();
                $callValidObj = false;
                $callValidObj = isset($varType);
                $callValidObj = ($callValidObj) ? $varType->getName() !== 'mixed' && class_exists($varType->getName()) : false;

                if ($callValidObj){
                    $this->validateObject($model, $var, $value);
                }else{
                    $var->setValue($model, $value);
                    foreach($atributes as $atribute){
                        $reflection = new ReflectionClass($atribute->getName());
                        $parentClass = $reflection->getParentClass();
                        $parentClassName = $parentClass ? $parentClass->getName() : null;
                        if ($parentClass && $parentClassName === AbstractValidation::class){
                            $provider = $this->getProviderByAtrubute($reflection);
                            $this->setAtrubuteArgs($provider, $atribute);
                            $result = $provider->isValid($value);
                            if(!$result){
                                $msg =  $this->getMessageByAtribute($atribute);
                                $this->errors[] = new ValidationException($msg);
                            }
                        }
                        unset($reflection);
                    }
    
                    return;
                }
                
            }else{
                if(!$enableNullValidation){
                    $this->errors[] = new ArgumentNotFoundException("Argumento '$varName' não encontrado na requisção");
                }
            }
        }

        private function validateObject(object &$model, ReflectionProperty $var, array $req){
            $typeName =  $var->getType()->getName();
            $reflectionClassReference = new ReflectionClass($typeName);
            $enableNullValidation = $this->isNullValidation($var);
            $validAtribute = $var->getAttributes(Valid::class);
            if(empty($validAtribute)){return;}
            $variables = $reflectionClassReference->getProperties();
            
            $modelInterno = $reflectionClassReference->newInstance();
            $var->setValue($model, $modelInterno);
            
            foreach($variables as $var){
                $this->validateField($modelInterno, $var, $req, $enableNullValidation);
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

    }
    

?>