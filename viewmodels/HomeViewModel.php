<?php

namespace ViewModels;

use Models\Category;
use Models\Course;
use Lib\ViewModel;

class FeaturedCourse extends Course
{
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
    )
    {
    }
}

class PageViewModel extends ViewModel
{
    public function __construct(
        public string $title
    )
    {
    }
}

