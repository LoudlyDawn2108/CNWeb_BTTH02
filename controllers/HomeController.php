<?php

use Lib\Controller;
use ViewModels\HomeIndexViewModel;

class HomeController extends Controller {
    public function index(): void
    {
        $featuredCourses = [];
        $categories = [];
        $viewModel = new HomeIndexViewModel(
            title: ""
        );
        
        $this->render('home/index', $viewModel); 
    }
}