<?php
namespace Models;
use Model;

require_once __DIR__ . '/../lib/Model.php';

class LessonTable {
    public function __toString(): string {
        return 'lessons';
    }
    public string $ID = 'lessons.id';
    public string $COURSE_ID = 'lessons.course_id';
    public string $TITLE = 'lessons.title';
    public string $CONTENT = 'lessons.content';
    public string $VIDEO_URL = 'lessons.video_url';
    public string $ORDER = 'lessons.order';
    public string $CREATED_AT = 'lessons.created_at';
}

class Lesson extends Model {
    protected ?string $table = 'lessons';

    public int $id;
    public int $course_id;
    public string $title;
    public ?string $content = null;
    public ?string $video_url = null;
    public int $order = 0;
    public ?string $created_at = null;
}