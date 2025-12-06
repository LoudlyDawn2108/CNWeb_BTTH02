<?php

namespace ViewModels;

class HomeIndexViewModel
{
    public function __construct(
        public string $title,
        public array  $featuredCourses,
        public array  $categories,
    ){}
}

class PageViewModel
{
    public string $title;
}

