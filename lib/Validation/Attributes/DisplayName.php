<?php

namespace Lib\Validation\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class DisplayName {
    public function __construct(public string $name) {}
}
