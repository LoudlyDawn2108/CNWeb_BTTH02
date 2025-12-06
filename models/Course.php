<?php
/**
 * Course Model
 * Handles all course-related database operations
 */
require_once __DIR__ . '/../lib/Model.php';

class Course extends Model {
    protected $table = 'courses';
}