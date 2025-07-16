<?php

    namespace Daniel\Validator;

    use Daniel\Origins\AnnotationsUtils;
    use Daniel\Origins\Aop\Aspect;
    use Daniel\Origins\Request;
    use Daniel\Validator\Exceptions\ValidationMethodNotAllowedException;
    use Daniel\Validator\Props\InjectValidation;
    use Daniel\Validator\Props\Valid;
    use Override;
    use ReflectionMethod;

    final class ValidatorAspect extends Aspect
    {

        #[Override]
        public function pointCut(object &$controllerEntity, ReflectionMethod &$method, array &$varArgs): bool{
            return AnnotationsUtils::isAnnotationPresent($method, Valid::class);
        }

        #[Override]
        public function aspectBefore(object &$controllerEntity, ReflectionMethod &$method, array &$varArgs){
            $validArgs = AnnotationsUtils::getAnnotationArgs($method, Valid::class);            
            assert(isset($validArgs[0]), "Argumentos inválidos: não foram encontrados argumentos.");
            
            $request = $this->getRequestBody();
            $validationClassReference = $validArgs[0];

            if(!isset($request)){
                throw new ValidationMethodNotAllowedException("Método não permitido para validação não possue uma 'Request' no metodo");
            }

            $validator = ValidatorManager::getValidator($validationClassReference, $request);
    
            $model = $validator->executeValidation();
            $injectModel = $this->injectModelValidation($method);
            $indexInject = $injectModel["index"] ?? -1;
            if($indexInject >= 0){
                $varArgs[$indexInject] = $model;
            }
        }

        #[Override]
        public function aspectAfter(object &$controllerEntity, ReflectionMethod &$method, array &$varArgs, mixed &$result){
            return $result;
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

        private function getRequestBody(){
            $body = file_get_contents('php://input');
            try {
                $jsonData = json_decode($body, true);
            } catch (\Throwable $th) {
                $jsonData = null;
            }

            return $jsonData;
        }

    }
    

?>