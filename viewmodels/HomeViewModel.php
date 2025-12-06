<?php

namespace ViewModels;

use Lib\ViewModel;

class HomeIndexViewModel extends ViewModel
{
    public function __construct(
        public string $title,
        public array  $featuredCourses,
        public array  $categories,
    ){}
}

class PageViewModel extends ViewModel
{
    public string $title;
}

