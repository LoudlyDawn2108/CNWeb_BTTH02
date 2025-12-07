<?php

namespace Lib\Validation\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Email implements ValidationAttribute {
    public function __construct(private string $message = "{field} must be a valid email address.") {}

    public function isValid(mixed $value): bool {
        if (empty($value)) return true; // Allow empty if not required
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function getMessage(): string {
        return $this->message;
    }
}
