<?php
/**
 * Material Model
 * Handles database operations for course materials (files)
 */
namespace Models;


use Functional\Collection;
use lib\Model;
use PDO;
require_once __DIR__ . '/../lib/Model.php';
require_once __DIR__ . '/../config/Database.php';

class Material extends Model {
    const TABLE = 'materials';
    const ID = 'id';
    const LESSON_ID = 'lesson_id';
    const FILENAME = 'filename';
    const FILE_PATH = 'file_path';
    const FILE_TYPE = 'file_type';
    const UPLOADED_AT = 'uploaded_at';

    protected ?string $table = self::TABLE;

    public int $id;
    public int $lesson_id;
    public string $filename;
    public string $file_path;
    public ?string $file_type = null;
    public ?string $uploaded_at = null;

    public function getByLesson(int $lessonId): Collection {
        $stmt = $this->db->prepare("SELECT * FROM materials WHERE lesson_id = ? ORDER BY created_at DESC");
        $stmt->execute([$lessonId]);
        return Collection::make($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public static function getFileType($filename): string {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    public static function isAllowedType($fileType): bool {
        $allowedTypes = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt', 'zip', 'rar'];
        return in_array(strtolower($fileType), $allowedTypes);
    }
}