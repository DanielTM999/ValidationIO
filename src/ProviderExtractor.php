<?php

    namespace Daniel\Validator\Props;

    use Daniel\Origins\AnnotationsUtils;
    use Daniel\Validator\BaseValidator;
    use Daniel\Validator\Exceptions\ArgumentNotFoundException;
    use ReflectionClass;
    use ReflectionObject;

    final class ProviderExtractor{

        public static function getProvider(string|ReflectionClass $validator): object{
            if (is_string($validator)) {
                $reflection = new \ReflectionClass($validator);
            } else {
                $reflection = $validator;
            }
            $className = $reflection->getName();
            if(AnnotationsUtils::isAnnotationPresent($reflection, ValidationProvider::class)){
                $providerList =  AnnotationsUtils::getAnnotationArgs($reflection, ValidationProvider::class);
                if(empty($providerList)){
                    throw new ArgumentNotFoundException("Atributo '$className' sem provedor");
                }

                return $providerList[0];
            }

            throw throw new ArgumentNotFoundException("Atributo '$className' sem provedor");
        }

    }
    
?>