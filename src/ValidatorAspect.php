<?php

    namespace Daniel\Validator;

    use Daniel\Origins\Aspect;
    use Daniel\Origins\Request;
    use Daniel\Validator\Exceptions\ValidationMethodNotAllowedException;
    use Daniel\Validator\Props\InjectValidation;
    use Daniel\Validator\Props\Valid;
    use Override;
    use ReflectionMethod;

    final class ValidatorAspect extends Aspect
    {

        #[Override]
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
            $validator = ValidatorManager::getValidator($validationClassReference, $request->getBody());
            $model = $validator->executeValidation();
            $injectModel = $this->injectModelValidation($method);
            $indexInject = $injectModel["index"] ?? -1;
            if($indexInject >= 0){
                $varArgs[$indexInject] = $model;
            }
        }

        #[Override]
        public function aspectAfter(object &$controllerEntity, ReflectionMethod &$method, array &$varArgs){
            
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