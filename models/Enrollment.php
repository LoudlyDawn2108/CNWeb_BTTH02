<?php
require_once __DIR__ . '/../lib/Model.php';

class Enrollment extends Model {
    const TABLE = 'enrollments';
    const ID = 'id';
    const COURSE_ID = 'course_id';
    const STUDENT_ID = 'student_id';
    const ENROLLED_DATE = 'enrolled_date';
    const STATUS = 'status';
    const PROGRESS = 'progress';

    protected ?string $table = self::TABLE;

    public int $id;
    public int $course_id;
    public int $student_id;
    public ?string $enrolled_date = null;
    public string $status = 'active';
    public int $progress = 0;

    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DROPPED = 'dropped';
}