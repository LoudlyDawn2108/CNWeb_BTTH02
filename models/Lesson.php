<?php
/**
 * Lesson Model
 * Handles all lesson-related database operations
 */
namespace Models;
use Lib\Model;
use Functional\Collection;
use Functional\Option;
// models/Lesson.php

use PDO;
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

    private $conn;
    public function getByCourse(int $courseId): Collection {
        // Sửa $this->db thành self::getConnection()
        $stmt = self::getConnection()->prepare("
        SELECT l.*, 
               COUNT(m.id) as material_count
        FROM lessons l
        LEFT JOIN materials m ON l.id = m.lesson_id
        WHERE l.course_id = ?
        GROUP BY l.id
        ORDER BY l.order ASC
    ");
        $stmt->execute([$courseId]);
        return Collection::make($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }
}
?>