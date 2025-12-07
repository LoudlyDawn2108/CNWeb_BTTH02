<?php

namespace Lib;

use ReflectionClass;
use ReflectionProperty;
use Lib\Validation\ModelState;
use Lib\Validation\Attributes\ValidationAttribute;
use Lib\Validation\Attributes\DisplayName;

abstract class ViewModel
{
    public ModelState $modelState;

    public function __construct()
    {
        $this->modelState = new ModelState();
    }

    /**
     * Bind Request data to ViewModel properties and Validate
     */
    public function handleRequest(array $data): void
    {
        $this->bind($data);
        $this->validate();
    }

    /**
     * Bind array data to public properties
     */
    public function bind(array $data): void
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Main validation entry point
     */
    public function validate(): bool
    {
        $this->modelState->clear();
        $this->validateAttributes();
        
        // Only run custom validation if basic attributes passed (optional strategy, but usually good)
        // Or run it regardless? Let's run it regardless so all errors show up.
        $this->validateCustom();

        return $this->modelState->isValid;
    }

    /**
     * Override this method to implement custom validation logic
     */
    protected function validateCustom(): void
    {
        // specific view models will implement this
    }

    /**
     * Validate properties based on Attributes
     */
    protected function validateAttributes(): void
    {
        $reflection = new ReflectionClass($this);
        
        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $propertyName = $property->getName();
            $value = $this->$propertyName ?? null;
            
            // Resolve Display Name
            $displayName = $propertyName;
            $displayAttributes = $property->getAttributes(DisplayName::class);
            if (!empty($displayAttributes)) {
                $displayName = $displayAttributes[0]->newInstance()->name;
            }

            // Check Validation Attributes
            $attributes = $property->getAttributes();
            foreach ($attributes as $attribute) {
                $instance = $attribute->newInstance();
                if ($instance instanceof ValidationAttribute) {
                    if (!$instance->isValid($value)) {
                        $message = $instance->getMessage();
                        // Replace {field} placeholder
                        $message = str_replace('{field}', $displayName, $message);
                        
                        $this->modelState->addError($propertyName, $message);
                    }
                }
            }
        }
    }
}