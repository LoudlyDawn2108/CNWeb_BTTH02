<?php
/**
 * Course Controller
 * Handles course listing, details, search, and filtering
 */

require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Enrollment.php';
require_once __DIR__ . '/../models/Lesson.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../viewmodels/CourseViewModels.php';


use Lib\Controller;
use Models\Category;
use Models\CategoryTable;
use Models\Course;
use Models\CourseTable;
use Models\Enrollment;
use Models\EnrollmentTable;
use Models\Lesson;
use Models\LessonTable;
use Models\UserTable;
use ViewModels\CourseDetailViewModel;
use ViewModels\CourseListViewModel;
use ViewModels\CourseSearchViewModel;
use ViewModels\CourseView;

class CourseController extends Controller {

    /**
     * Display all courses with filtering
     */
    public function index(): void {
        $categoryId = $this->getQuery('category');
        $level = $this->getQuery('level');
        $search = $this->getQuery('search');

        $page = max(1, intval($this->getQuery('page', 1)));
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $c = new CourseTable();
        $cat = new CategoryTable();
        $e = new EnrollmentTable();
        $u = new UserTable();

        // Build Query
        $query = Course::query()
                       ->select(
                           ["$c.*", "$cat->NAME as category_name", "$u->FULLNAME as instructor_name",
                               "(SELECT COUNT(*) FROM $e WHERE $e->COURSE_ID = $c->ID) as enrollment_count"])
                       ->table($c)
                       ->leftJoin($cat, $c->CATEGORY_ID, '=', $cat->ID)
                   ->leftJoin($u, $c->INSTRUCTOR_ID, '=', $u->ID)
                       ->where($c->STATUS, "approved");

        if ($categoryId) {
            $query->where($c->CATEGORY_ID, $categoryId);
        }
        if ($level) {
            $query->where($c->LEVEL, $level);
        }
        if ($search) {
            $query->whereRaw(
                "($c->TITLE LIKE :search_title OR $c->DESCRIPTION LIKE :search_desc)",
                [
                    ":search_title" => '%' . $search . '%',
                    ":search_desc" => '%' . $search . '%'
                ]);
        }

        // Clone query for counting total results (simplified count)
        $countQuery = clone $query;
        $totalCourses = $countQuery->count();

        $courses = $query
            ->orderBy($c->CREATED_AT, 'DESC')
            ->limit($limit)
            ->offset($offset)
            ->get(CourseView::class);

        $categories = Category::all();

        $filters = [
            'category_id' => $categoryId,
            'level' => $level,
            'search' => $search
        ];

        $viewModel = new CourseListViewModel(
            title:       'Khóa học - Online Course',
            courses:     $courses,
            categories:  $categories,
            filters:     $filters,
            currentPage: $page,
            totalPages:  ceil($totalCourses / $limit),
            levels:      ['Beginner', 'Intermediate', 'Advanced']
        );

        $this->render('courses/index', $viewModel);
    }

    /**
     * Display course details
     */
    public function detail($id): void {
        $c = new CourseTable();
        $cat = new CategoryTable();
        $u = new UserTable();
        $e = new EnrollmentTable();
        $l = new LessonTable();

        /** @var CourseView $course */
        $course = Course::query()
                        ->select(
                            ["$c.*", "$cat->NAME as category_name", "$u->FULLNAME as instructor_name",
                                "(SELECT COUNT(*) FROM $e WHERE $e->COURSE_ID = $c->ID) as enrollment_count",
                                "(SELECT COUNT(*) FROM $l WHERE $l->COURSE_ID = $c->ID) as lesson_count"])
                        ->table($c)
                        ->leftJoin($cat, $c->CATEGORY_ID, '=', $cat->ID)
                        ->leftJoin($u, $c->INSTRUCTOR_ID, '=', $u->ID)
                        ->where($c->ID, $id)
                        ->first(CourseView::class);

        if (!$course) {
            http_response_code(404);
            echo 'Khóa học không tồn tại.';
            exit;
        }

        $lessons = Lesson::query()
                         ->where($l->COURSE_ID, $id)
                         ->orderBy($l->ORDER)
                         ->get(Lesson::class);

        $isEnrolled = false;
        $enrollment = null;

        $currentUserOption = $this->user();
        $currentUser = null;

        $currentUserOption->match(
            function ($user) use (&$enrollment, &$isEnrolled, &$currentUser, $id) {
                $currentUser = $user;
                $e = new EnrollmentTable();
                $enrollmentObj = Enrollment::query()
                                           ->where($e->STUDENT_ID, $user['id'])
                                           ->where($e->COURSE_ID, $id)
                                           ->first();

                if ($enrollmentObj) {
                    $enrollment = $enrollmentObj->toArray();
                    $isEnrolled = true;
                }
            },
            fn() => null
        );

        $relatedCourses = Course::query()
                                ->select(["$c.*", "$u->FULLNAME as instructor_name"])
                                ->table($c)
                                ->leftJoin($u, $c->INSTRUCTOR_ID, '=', $u->ID)
                                ->where($c->CATEGORY_ID, $course->category_id)
                                ->where($c->STATUS, 'approved')
                                ->orderBy($c->CREATED_AT, 'DESC')
                                ->limit(5) // Get 5 to filter self out later or just use where != id
                                ->get(CourseView::class);

        $relatedCourses = array_filter($relatedCourses, fn($c) => $c->id != $id);
        $relatedCourses = array_slice($relatedCourses, 0, 4);

        $viewModel = new CourseDetailViewModel(
            title:          $course->title . ' - Online Course',
            course:         $course,
            lessons:        $lessons,
            isEnrolled:     $isEnrolled,
            relatedCourses: $relatedCourses,
            enrollment:     $enrollment,
            currentUser:    $currentUser
        );

        $this->render('courses/detail', $viewModel);
    }

    /**
     * Search courses
     */
    public function search(): void {
        $keyword = $this->getQuery('q', '');
        $courses = [];

        if (!empty($keyword)) {
            $c = new CourseTable();
            $cat = new CategoryTable();
            $u = new UserTable();

            $courses = Course::query()
                             ->select(["$c.*", "$cat->NAME as category_name", "$u->FULLNAME as instructor_name"])
                             ->table($c)
                             ->leftJoin($cat, $c->CATEGORY_ID, '=', $cat->ID)
                             ->leftJoin($u, $c->INSTRUCTOR_ID, '=', $u->ID)
                             ->where($c->STATUS, 'approved')
                             ->whereRaw(
                                 "($c->TITLE LIKE :keyword_title OR $c->DESCRIPTION LIKE :keyword_desc)",
                                 [
                                     ':keyword_title' => '%' . $keyword . '%',
                                     ':keyword_desc' => '%' . $keyword . '%'
                                 ])
                             ->orderBy($c->CREATED_AT, 'DESC')
                             ->limit(20)
                             ->get(CourseView::class);
        }

        $categories = Category::all();

        $viewModel = new CourseSearchViewModel(
            title:      'Tìm kiếm: ' . htmlspecialchars($keyword) . ' - Online Course',
            courses:    $courses,
            keyword:    $keyword,
            categories: $categories
        );

        $this->render('courses/search', $viewModel);
    }

}
