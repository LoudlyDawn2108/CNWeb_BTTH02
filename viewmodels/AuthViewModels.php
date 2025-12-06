<?php

namespace ViewModels;

use Lib\ViewModel;

class AuthLoginViewModel extends ViewModel
{
    public function __construct(
        public string $title
    )
    {
    }
}

class AuthRegisterViewModel extends ViewModel
{
    public function __construct(
        public string $title,
        public array  $old = [],
    )
    {
    }
}

