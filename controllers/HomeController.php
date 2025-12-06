<?php

use Lib\Controller;
use ViewModels\HomeIndexViewModel;

class HomeController extends Controller
{
    public function index(): void
    {
        $featuredCourses = [];
        $categories = [];
        $viewModel = new HomeIndexViewModel(
            title: "Trang chủ - Feetcode",
            featuredCourses: $featuredCourses,
            categories: $categories
        );

        $this->render('home/index', $viewModel);
    }
}