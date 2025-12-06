<?php
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../viewmodels/HomeViewModel.php';

use Lib\Controller;
use ViewModels\HomeIndexViewModel;

class HomeController extends Controller
{
    public function index(): void
    {
        $featuredCourses = Course::query()
            ->select(['c.*', 'cat.name as category_name', 'u.fullname as instructor_name'])
            ->table('courses c')
            ->leftJoin('categories cat', 'c.category_id', '=', 'cat.id')
            ->leftJoin('users u', 'c.instructor_id', '=', 'u.id')
            ->where('c.status', 'approved')
            ->orderBy('c.created_at', 'DESC')
            ->limit(6)
            ->get();

        $featuredCourses = array_map(fn($c) => $c->toArray(), $featuredCourses);

        $categories = Category::all();
        $categories = array_map(fn($c) => $c->toArray(), $categories);


        $viewModel = new HomeIndexViewModel(
            title: "Trang chủ - Feetcode",
            featuredCourses: $featuredCourses,
            categories: $categories
        );

        $this->render('home/index', $viewModel);
    }
}