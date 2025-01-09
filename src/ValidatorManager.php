<?php

    namespace Daniel\Validator;

    use Daniel\Validator\Exceptions\ArgumentNotFoundException;
    use Daniel\Validator\Exceptions\ValidationException;
    use Daniel\Validator\Exceptions\ValidationMethodNotAllowedException;
    use Daniel\Validator\Props\AbstractValidation;
    use Daniel\Validator\Props\NullableValidation;
    use Daniel\Validator\Props\Valid;
    use Daniel\Validator\Props\ValidationProvider;
    use ReflectionAttribute;
    use ReflectionClass;
    use ReflectionProperty;

    final class ValidatorManager 
    {
        private function __construct(private string $className, private array $obj) {
            $this->className = $className;
            $this->obj = $obj;
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
            $enableNullValidation = $this->isNullValidation($reflectionClassReference);

            $variables = $reflectionClassReference->getProperties();
            $model = null;
            try {
                $model = $reflectionClassReference->newInstance();
            } catch (\Throwable $th) {

            }

            foreach($variables as $var){
                $this->validateField($model, $var, $request, $enableNullValidation);
            }
            $indexInject = $injectModel["index"] ?? -1;
            if($indexInject >= 0){
                $varArgs[$indexInject] = $model;
            }

            return $model;
        }

        private function validateField(object &$model, ReflectionProperty $var, array &$reqBody, bool $ignoreIfNull = false){
            $varName = $var->getName();
            $atributes = $var->getAttributes();
            if(isset($reqBody[$varName])){
                $value = $reqBody[$varName];
                $varType = $var->getType();
                $callValidObj = false;
                $callValidObj = isset($varType);
                $callValidObj = ($callValidObj) ? $varType->getName() !== 'mixed' && class_exists($varType->getName()) : false;

                if ($callValidObj){
                    $this->validateObject($model, $var, $value, $value);
                }else{
                    $var->setValue($model, $value);
                    foreach($atributes as $atribute){
                        $reflection = new ReflectionClass($atribute->getName());
                        $parentClass = $reflection->getParentClass();
                        $parentClassName = $parentClass ? $parentClass->getName() : null;
                        if ($parentClass && $parentClassName === AbstractValidation::class){
                            $provider = $this->getProviderByAtrubute($atribute);
                            $result = $provider->isValid($value);
                            if(!$result){
                                $msg =  $this->getMessageByAtribute($atribute);
                                throw new ValidationException($msg);
                            }
                        }
                        unset($reflection);
                    }
    
                    return;
                }
                
            }else{
                if(!$ignoreIfNull){
                    throw new ArgumentNotFoundException("Argumento '$varName' não encontrado na requisção");
                }
            }
        }

        private function validateObject(object &$model, ReflectionProperty $var, array $req, &$value){
            $typeName =  $var->getType()->getName();
            $reflectionClassReference = new ReflectionClass($typeName);
            $enableNullValidation = $this->isNullValidation($reflectionClassReference);
            $validAtribute = $var->getAttributes(Valid::class);
            if(empty($validAtribute)){return;}
            $variables = $reflectionClassReference->getProperties();
            
            $modelInterno = $reflectionClassReference->newInstance();
            $var->setValue($model, $modelInterno);
            
            foreach($variables as $var){
                $this->validateField($modelInterno, $var, $req, $enableNullValidation);
            }
        }

        private function getProviderByAtrubute(ReflectionAttribute $attr): BaseValidator{
            $AttName = $attr->getName();
            $reflect = new ReflectionClass($AttName);
            $providerAttr = $reflect->getAttributes(ValidationProvider::class);

            if(empty($providerAttr)){
                throw new ArgumentNotFoundException("Atributo '$AttName' sem provedor");
            }
            $providerList = $providerAttr[0]->getArguments();

            if(empty($providerList)){
                throw new ArgumentNotFoundException("Atributo '$AttName' sem provedor");
            }

            return $providerList[0];

        }

        private function getMessageByAtribute(ReflectionAttribute $attr){
            $instance = $attr->newInstance();
            return $instance->getMessage();
        }

        private function isNullValidation(ReflectionClass $reflectionClassReference): bool{
            $enableNullValidation = false;

            try {
                $atributes = $reflectionClassReference->getAttributes(NullableValidation::class);
                
                
                if(!empty($atributes)){
                    $targetAtrubute =  $atributes[0];
                    
                    $atributesArgs = $targetAtrubute->getArguments();

                    if(!empty($atributesArgs)){
                        $enableNullValidation = $atributesArgs[0];
                    }
                }
                return $enableNullValidation;
            } catch (\Throwable $th) {
                return $enableNullValidation;
            }
        }

    }
    

?>