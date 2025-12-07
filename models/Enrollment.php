<?php
namespace Models;
use Model;

require_once __DIR__ . '/../lib/Model.php';

class EnrollmentTable {
    public function __toString(): string {
        return 'enrollments';
    }
    public string $ID = 'enrollments.id';
    public string $COURSE_ID = 'enrollments.course_id';
    public string $STUDENT_ID = 'enrollments.student_id';
    public string $ENROLLED_DATE = 'enrollments.enrolled_date';
    public string $STATUS = 'enrollments.status';
    public string $PROGRESS = 'enrollments.progress';
}

class Enrollment extends Model {
    protected ?string $table = 'enrollments';

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