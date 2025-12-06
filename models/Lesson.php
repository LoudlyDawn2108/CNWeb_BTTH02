<?php
require_once __DIR__ . '/../lib/Model.php';

class Lesson extends Model {
    const TABLE = 'lessons';
    const ID = 'id';
    const COURSE_ID = 'course_id';
    const TITLE = 'title';
    const CONTENT = 'content';
    const VIDEO_URL = 'video_url';
    const ORDER = 'order';
    const CREATED_AT = 'created_at';

    protected ?string $table = self::TABLE;

    public int $id;
    public int $course_id;
    public string $title;
    public ?string $content = null;
    public ?string $video_url = null;
    public int $order = 0;
    public ?string $created_at = null;
}