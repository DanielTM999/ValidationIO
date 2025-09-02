<?php

    namespace Daniel\Validator\Provider;

    use Daniel\Validator\BaseValidator;

    final class LengthValidator extends BaseValidator
    {
        public const TYPE_MIN = '-';
        public const TYPE_MAX = '+';
        private readonly string $type;

        public function __construct(string $type = self::TYPE_MIN) {
            $this->type = $type;
        }

        public function isValid($value): bool
        {
            if($value === null) return true;
            if (!is_string($value)) {
                return false;
            }
            $length = strlen($value);
            $baseSize = $this->getBaseSize();
            
            if ($this->type === self::TYPE_MIN) {
                return $length >= $baseSize;
            } else {
                return $length <= $baseSize;
            }
        }

        private function getbaseSize(): int{
            if($this->type === self::TYPE_MIN){
                return $this->getParamArgs()[0] ?? 5;
            }else {
                return $this->getParamArgs()[0] ?? 50;
            }
        }

    }

?>
