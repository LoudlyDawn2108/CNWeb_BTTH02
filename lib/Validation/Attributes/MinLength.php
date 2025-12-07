<?php

namespace Lib\Validation\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class MinLength implements ValidationAttribute {
    public function __construct(private int $length, private string $message = "") {
        if (empty($this->message)) {
            $this->message = "{field} must be at least {$this->length} characters long.";
        }
    }

    public function isValid(mixed $value): bool {
        if (empty($value)) return true;
        return strlen((string)$value) >= $this->length;
    }

    public function getMessage(): string {
        return $this->message;
    }
}
