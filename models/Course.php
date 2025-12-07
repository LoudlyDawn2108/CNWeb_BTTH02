<?php
namespace Models;
use Model;

require_once __DIR__ . '/../lib/Model.php';

class CourseTable {
    public function __toString(): string {
        return 'courses';
    }
    public string $ID = 'courses.id';
    public string $TITLE = 'courses.title';
    public string $DESCRIPTION = 'courses.description';
    public string $INSTRUCTOR_ID = 'courses.instructor_id';
    public string $CATEGORY_ID = 'courses.category_id';
    public string $PRICE = 'courses.price';
    public string $DURATION_WEEKS = 'courses.duration_weeks';
    public string $LEVEL = 'courses.level';
    public string $IMAGE = 'courses.image';
    public string $STATUS = 'courses.status';
    public string $CREATED_AT = 'courses.created_at';
    public string $UPDATED_AT = 'courses.updated_at';
}

class Course extends Model {
    protected ?string $table = 'courses';

    public int $id;
    public string $title;
    public ?string $description = null;
    public int $instructor_id;
    public int $category_id;
    public float $price = 0.00;
    public int $duration_weeks = 1;
    public string $level = 'Beginner';
    public ?string $image = null;
    public string $status = 'pending';
    public ?string $created_at = null;
    public ?string $updated_at = null;
}