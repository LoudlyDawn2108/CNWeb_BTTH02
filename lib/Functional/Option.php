<?php

namespace Functional;


abstract class Option {
    abstract public function map(callable $f): Option;
    abstract public function flatMap(callable $f): Option;
    abstract public function getOrElse($default);
    abstract public function match(callable $ifSome, callable $ifNone);
    
    public static function some($value): Option {
        return new Some($value);
    }
    
    public static function none(): Option {
        return new None();
    }

    public static function fromNullable($value): Option {
        return $value === null ? self::none() : self::some($value);
    }
}

class Some extends Option {
    private $value;
    
    public function __construct($value) {
        $this->value = $value;
    }
    
    public function map(callable $f): Option {
        return Option::some($f($this->value));
    }
    
    public function flatMap(callable $f): Option {
        return $f($this->value);
    }
    
    public function getOrElse($default) {
        return $this->value;
    }
    
    public function match(callable $ifSome, callable $ifNone) {
        return $ifSome($this->value);
    }
}

class None extends Option {
    public function map(callable $f): Option {
        return $this;
    }
    
    public function flatMap(callable $f): Option {
        return $this;
    }
    
    public function getOrElse($default) {
        return $default;
    }
    
    public function match(callable $ifSome, callable $ifNone) {
        return $ifNone();
    }
}
