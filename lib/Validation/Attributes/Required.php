<?php

namespace Lib\Validation\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Required implements ValidationAttribute {
    public function __construct(private string $message = "{field} is required.") {}

    public function isValid(mixed $value): bool {
        if (is_null($value)) return false;
        if (is_string($value) && trim($value) === '') return false;
        return true;
    }

    public function getMessage(): string {
        return $this->message;
    }
}
