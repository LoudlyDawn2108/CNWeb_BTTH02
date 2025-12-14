<?php

namespace ViewModels;

use Lib\ViewModel;
use Lib\Validation\Attributes\Required;
use Lib\Validation\Attributes\DisplayName;

class EnrollViewModel extends ViewModel
{
    #[Required("Khóa học không hợp lệ")]
    #[DisplayName("Course ID")]
    public int $course_id = 0;

    public function __construct()
    {
        parent::__construct();
    }
}

class UnenrollViewModel extends ViewModel
{
    #[Required("Khóa học không hợp lệ")]
    #[DisplayName("Course ID")]
    public int $course_id = 0;

    public function __construct()
    {
        parent::__construct();
    }
}
