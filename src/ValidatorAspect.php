<?php

    namespace Daniel\Validator;

use Attribute;
use Daniel\Origins\Aspect;
    use Daniel\Origins\Request;
    use Daniel\Validator\Exceptions\ArgumentNotFoundException;
use Daniel\Validator\Exceptions\ValidationException;
use Daniel\Validator\Exceptions\ValidationMethodNotAllowedException;
    use Daniel\Validator\Props\AbstractValidation;
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
            foreach($variables as $var){
                $this->validateField($var, $request);
            }
        }

        private function validateField(ReflectionProperty $var, Request $req, bool $ignoreIfNull = false){
            $varName = $var->getName();
            $reqBody = $req->getBody();
            $atributes = $var->getAttributes();
            if(isset($reqBody[$varName])){
                $value = $reqBody[$varName];

                foreach($atributes as $atribute){
                    $reflection = new ReflectionClass($atribute->getName());
                    $parentClass = $reflection->getParentClass();
                    $parentClassName = $parentClass->getName();
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
            if(!$ignoreIfNull){
                throw new ArgumentNotFoundException("Argumento '$varName' não encontrado na requisção");
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
    }
    

?>