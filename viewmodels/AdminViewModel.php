<?php

namespace ViewModels;

use Lib\ViewModel;
use Lib\Validation\Attributes\Required;
use Lib\Validation\Attributes\MinLength;
use Lib\Validation\Attributes\DisplayName;
use Models\Category;
use Models\CategoryTable;

class AdminDashboardViewModel extends ViewModel
{
    public function __construct(
        public string $title = "",
        public int $totalUsers = 0,
        public int $totalStudents = 0,
        public int $totalInstructors = 0,
        public int $totalCourses = 0,
        public int $totalEnrollments = 0,
        public array $pendingCourses = [],
        public array $recentUsers = []
    ) {
        parent::__construct();
    }
}

class AdminUsersViewModel extends ViewModel
{
    public function __construct(
        public string $title = "",
        public array $users = [],
        public array $roleStats = [],
        public int $currentPage = 1,
        public int $totalPages = 1,
        public int $totalUsers = 0,
        public string $search = "",
        public string $roleFilter = "",
        public string $statusFilter = ""
    ) {
        parent::__construct();
    }
}

class AdminCategoriesViewModel extends ViewModel
{
    public function __construct(
        public string $title = "",
        public array $categories = [],
        public array $stats = [],
        public int $currentPage = 1,
        public int $totalPages = 1,
        public string $search = ""
    ) {
        parent::__construct();
    }
}

class AdminCategoryFormViewModel extends ViewModel
{
    #[Required]
    #[MinLength(3)]
    #[DisplayName("Tên danh mục")]
    public string $name = '';

    #[DisplayName("Mô tả")]
    public string $description = '';
    
    // Non-bound properties for view state
    public int $id = 0;
    public bool $isEdit = false;
    public ?array $category = null;

    public function __construct(
        public string $title = "Quản lý danh mục"
    ) {
        parent::__construct();
    }
    
    protected function validateCustom(): void
    {
        $cat = new CategoryTable();
        
        // Description length check
        if (!empty($this->description) && strlen($this->description) > 500) {
            $this->modelState->addError('description', 'Mô tả không được quá 500 ký tự');
        }
        
        // Duplicate Name Check
        if (!empty($this->name)) {
            $query = Category::query()
                ->whereRaw('LOWER(' . $cat->NAME . ') = LOWER(:name)', [':name' => $this->name]);
                
            // If editing, exclude current ID
            if ($this->isEdit && $this->id > 0) {
                $query->whereRaw($cat->ID . ' != :id', [':id' => $this->id]);
            }
            
            if ($query->first()) {
                $this->modelState->addError('name', 'Tên danh mục đã tồn tại');
            }
        }
    }
}

class AdminStatisticsViewModel extends ViewModel
{
    public function __construct(
        public string $title = "",
        public array $userStats = [],
        public array $courseStats = [],
        public array $enrollmentStats = [],
        public array $categoryStats = [],
        public array $topInstructors = [],
        public array $popularCourses = [],
        public array $monthlyUsers = []
    ) {
        parent::__construct();
    }
}