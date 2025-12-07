<?php
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/User.php'; // Add User model include
require_once __DIR__ . '/../viewmodels/HomeViewModel.php';

use Lib\Controller;
use Models\Category;
use Models\CategoryTable;
use Models\Course;
use Models\CourseTable;
use Models\User;
use Models\UserTable;
use ViewModels\HomeIndexViewModel;
use ViewModels\FeaturedCourse;

class HomeController extends Controller
{
    public function index(): void
    {
        $c = new CourseTable();
        $cat = new CategoryTable();
        $u = new UserTable();

        $featuredCourses = Course::query()
            ->select([
                "$c.*", 
                "$cat->NAME as category_name", 
                "$u->FULLNAME as instructor_name"
            ])
            ->table($c)
            ->leftJoin($cat, $c->CATEGORY_ID, '=', $cat->ID)
            ->leftJoin($u, $c->INSTRUCTOR_ID, '=', $u->ID)
            ->where($c->STATUS, 'approved')
            ->orderBy($c->CREATED_AT, 'DESC')
            ->limit(6)
            ->get(FeaturedCourse::class);

        // Fetch categories with course count
        $categories = Category::query()
            ->select([
                "$cat.*",
                "COUNT($c->ID) as course_count"
            ])
            ->table($cat)
            ->leftJoin($c, $cat->ID, '=', $c->CATEGORY_ID)
            ->groupBy($cat->ID)
            ->get(Category::class);

        $viewModel = new HomeIndexViewModel(
            title: "Trang chủ - Feetcode",
            featuredCourses: $featuredCourses,
            categories: $categories
        );

        $this->render('home/index', $viewModel);
    }
}