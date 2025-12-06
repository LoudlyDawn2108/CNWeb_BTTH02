<?php

namespace Functional;

use Exception;
use Throwable;

abstract class Result {
    abstract public function map(callable $f): Result;
    abstract public function mapErr(callable $f): Result;
    abstract public function flatMap(callable $f): Result;
    abstract public function match(callable $ifOk, callable $ifErr);
    abstract public function isOk(): bool;
    abstract public function unwrap();

    public static function ok($value): Result {
        return new Ok($value);
    }

    public static function err($error): Result {
        return new Err($error);
    }
    
    // Helper to wrap a function that might throw
    public static function try(callable $f): Result {
        try {
            return self::ok($f());
        } catch (Throwable $e) {
            return self::err($e);
        }
    }
}

class Ok extends Result {
    private $value;

    public function __construct($value) {
        $this->value = $value;
    }

    public function map(callable $f): Result {
        return Result::ok($f($this->value));
    }

    public function mapErr(callable $f): Result {
        return $this;
    }

    public function flatMap(callable $f): Result {
        return $f($this->value);
    }

    public function match(callable $ifOk, callable $ifErr) {
        return $ifOk($this->value);
    }

    public function isOk(): bool {
        return true;
    }
    
    public function unwrap() {
        return $this->value;
    }
}

class Err extends Result {
    private $error;

    public function __construct($error) {
        $this->error = $error;
    }

    public function map(callable $f): Result {
        return $this;
    }

    public function mapErr(callable $f): Result {
        return Result::err($f($this->error));
    }

    public function flatMap(callable $f): Result {
        return $this;
    }

    public function match(callable $ifOk, callable $ifErr) {
        return $ifErr($this->error);
    }

    public function isOk(): bool {
        return false;
    }
    
    public function unwrap() {
        throw new Exception("Called unwrap on Err: " . json_encode($this->error));
    }
}
