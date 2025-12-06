<?php

require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/User.php'; // Add User model include
require_once __DIR__ . '/../viewmodels/HomeViewModel.php';

use Lib\Controller;
use Models\Category;
use Models\Course;
use Models\User;
use ViewModels\HomeIndexViewModel;
use ViewModels\FeaturedCourse;

class HomeController extends Controller
{
    public function index(): void
    {
        // Aliases for tables
        $c = 'c';
        $cat = 'cat';
        $u = 'u';

        $featuredCourses = Course::query()
            ->select([
                "$c.*", 
                "$cat." . Category::NAME . ' as category_name', 
                "$u." . User::FULLNAME . ' as instructor_name'
            ])
            ->table(Course::TABLE . " $c")
            ->leftJoin(Category::TABLE . " $cat", "$c." . Course::CATEGORY_ID, '=', "$cat." . Category::ID)
            ->leftJoin(User::TABLE . " $u", "$c." . Course::INSTRUCTOR_ID, '=', "$u." . User::ID)
            ->where("$c." . Course::STATUS, 'approved')
            ->orderBy("$c." . Course::CREATED_AT, 'DESC')
            ->limit(6)
            ->get(FeaturedCourse::class);

        // Fetch categories with course count
        $catAlias = 'cat';
        $courseAlias = 'c';

        $categories = Category::query()
            ->select([
                "$catAlias.*",
                "COUNT($courseAlias." . Course::ID . ") as course_count"
            ])
            ->table(Category::TABLE . " $catAlias")
            ->leftJoin(Course::TABLE . " $courseAlias", "$catAlias." . Category::ID, '=', "$courseAlias." . Course::CATEGORY_ID)
            ->groupBy("$catAlias." . Category::ID)
            ->get(Category::class);

        $viewModel = new HomeIndexViewModel(
            title: "Trang chủ - Feetcode",
            featuredCourses: $featuredCourses,
            categories: $categories
        );

        $this->render('home/index', $viewModel);
    }
}