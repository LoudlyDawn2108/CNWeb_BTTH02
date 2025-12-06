<?php
require_once __DIR__ . '/../lib/Model.php';

class Enrollment extends Model {
    protected ?string $table = 'enrollments';

    const string STATUS_ACTIVE = 'active';
    const string STATUS_COMPLETED = 'completed';
    const string STATUS_DROPPED = 'dropped';
}