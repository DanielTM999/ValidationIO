<?php

    namespace Daniel\Validator;


    use Daniel\Origins\Aspect;
    use Daniel\Origins\Request;
    use Daniel\Validator\Exceptions\ArgumentNotFoundException;
    use Daniel\Validator\Exceptions\ValidationException;
    use Daniel\Validator\Exceptions\ValidationMethodNotAllowedException;
    use Daniel\Validator\Props\AbstractValidation;
    use Daniel\Validator\Props\InjectValidation;
    use Daniel\Validator\Props\Valid;
    use Daniel\Validator\Props\ValidationProvider;
    use ReflectionAttribute;
    use ReflectionClass;
    use ReflectionMethod;
    use ReflectionProperty;

    final class ValidatorAspect extends Aspect
    {

        public function aspectBefore(object &$controllerEntity, ReflectionMethod &$method, array &$varArgs){
            $validAtribute = $method->getAttributes(Valid::class);
            if(empty($validAtribute)){return;}
            $validArgs = $validAtribute[0]->getArguments();
            
            assert(isset($validArgs[0]), "Argumentos inválidos: não foram encontrados argumentos.");
            
            $request = null;
            $validationClassReference = $validArgs[0];

            foreach($varArgs as $arg){
                if (isset($arg) && $arg instanceof Request){
                    $request = $arg;
                    break;
                }
            }

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

            $injectModel = $this->injectModelValidation($method);

            foreach($variables as $var){
                $this->validateField($model, $var, $request, false);
            }
            $indexInject = $injectModel["index"] ?? -1;
            if($indexInject >= 0){
                $varArgs[$indexInject] = $model;
            }
        }

        private function validateField(object &$model, ReflectionProperty $var, Request $req, bool $ignoreIfNull = false){
            $varName = $var->getName();
            $reqBody = $req->getBody();
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

        private function validateFieldRecursive(object &$model, ReflectionProperty $var, array &$reqBody, bool $ignoreIfNull = false){
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
            $validAtribute = $var->getAttributes(Valid::class);
            if(empty($validAtribute)){return;}
            $variables = $reflectionClassReference->getProperties();
            
            $modelInterno = $reflectionClassReference->newInstance();
            $var->setValue($model, $modelInterno);
            
            foreach($variables as $var){
                $this->validateFieldRecursive($modelInterno, $var, $req, false);
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

        private function injectModelValidation(ReflectionMethod &$method){
            $index = 0;
            foreach ($method->getParameters() as $param){
                $attributes = $param->getAttributes(InjectValidation::class);
                if (!empty($attributes)){
                    return [
                        "var" => $param,
                        "index" => $index
                    ];
                }
                $index++;
            }
            return null;
        }

    }
    

?>