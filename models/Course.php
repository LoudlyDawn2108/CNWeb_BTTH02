<?php
namespace Models;
use Model;

require_once __DIR__ . '/../lib/Model.php';

class Course extends Model {
    const TABLE = 'courses';
    const ID = 'id';
    const TITLE = 'title';
    const DESCRIPTION = 'description';
    const INSTRUCTOR_ID = 'instructor_id';
    const CATEGORY_ID = 'category_id';
    const PRICE = 'price';
    const DURATION_WEEKS = 'duration_weeks';
    const LEVEL = 'level';
    const IMAGE = 'image';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected ?string $table = self::TABLE;

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