<?php

require_once __DIR__ . '/../lib/Model.php';

use Functional\Option;
use Functional\Result;

class Enrollment extends Model {
    protected ?string $table = 'enrollments';

    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DROPPED = 'dropped';
}