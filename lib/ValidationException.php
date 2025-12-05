<?php

namespace Lib;

class ValidationException extends \Exception {
    public array $errors {
        get {
            return $this->errors;
        }
    }
    public array $old {
        get {
            return $this->old;
        }
    }

    public function __construct(array $errors, array $old = []) {
        parent::__construct("Validation Failed");
        $this->errors = $errors;
        $this->old = $old;
    }

}
