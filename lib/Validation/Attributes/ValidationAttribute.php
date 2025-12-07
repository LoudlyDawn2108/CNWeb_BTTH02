<?php

namespace Lib\Validation\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
interface ValidationAttribute {
    public function isValid(mixed $value): bool;
    public function getMessage(): string;
}
