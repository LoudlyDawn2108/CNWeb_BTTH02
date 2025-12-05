<?php

namespace Lib;

abstract class FormRequest {
    
    public function __construct() {
        // Hydrate public properties from request data
        $data = array_merge($_GET, $_POST);
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
        
        // Cast typed properties if needed (basic scalar support)
        $this->castProperties();

        // Validate
        $this->validate();
    }

    private function castProperties() {
        $reflection = new \ReflectionClass($this);
        foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->isInitialized($this)) {
                continue;
            }
            $type = $property->getType();
            if ($type && !$type->allowsNull() && $property->getValue($this) === null) {
                 // If strictly typed and null, it might be an issue, but let hydrate handle raw values.
                 // We can cast basic types here.
            }
            
            $value = $property->getValue($this);
            if ($value !== null && $type instanceof \ReflectionNamedType) {
                $typeName = $type->getName();
                if ($typeName === 'int') {
                    $property->setValue($this, (int)$value);
                } elseif ($typeName === 'float') {
                    $property->setValue($this, (float)$value);
                } elseif ($typeName === 'bool') {
                    $property->setValue($this, (bool)$value);
                }
            }
        }
    }

    /**
     * @throws ValidationException
     */
    abstract protected function validate(): void;
}
