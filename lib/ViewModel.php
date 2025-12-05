<?php

namespace Lib;

abstract class ViewModel {
    public function __construct(array $data = []) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
    
    public static function from(array $data): static {
        return new static($data);
    }
}
