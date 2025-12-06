<?php

namespace ViewModels;

use Lib\ViewModel;
use Course;
use Category;

class FeaturedCourse extends Course {
    public ?string $category_name = null;
    public ?string $instructor_name = null;
}

class HomeIndexViewModel extends ViewModel
{
    /**
     * @param string $title
     * @param FeaturedCourse[] $featuredCourses
     * @param Category[] $categories
     */
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

