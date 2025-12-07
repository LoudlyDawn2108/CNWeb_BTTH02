<?php

namespace Lib\Validation;

class ModelState {
    private array $errors = [];
    public bool $isValid = true;

    public function addError(string $key, string $message): void {
        if (!isset($this->errors[$key])) {
            $this->errors[$key] = [];
        }
        $this->errors[$key][] = $message;
        $this->isValid = false;
    }

    public function getErrors(string $key): array {
        return $this->errors[$key] ?? [];
    }

    public function getFirstError(string $key): ?string {
        return $this->errors[$key][0] ?? null;
    }

    public function hasError(string $key): bool {
        return !empty($this->errors[$key]);
    }
    
    public function clear(): void {
        $this->errors = [];
        $this->isValid = true;
    }
}
