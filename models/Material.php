<?php
namespace Models;
use Model;

require_once __DIR__ . '/../lib/Model.php';

class MaterialTable {
    public function __toString(): string {
        return 'materials';
    }
    public string $ID = 'materials.id';
    public string $LESSON_ID = 'materials.lesson_id';
    public string $FILENAME = 'materials.filename';
    public string $FILE_PATH = 'materials.file_path';
    public string $FILE_TYPE = 'materials.file_type';
    public string $UPLOADED_AT = 'materials.uploaded_at';
}

class Material extends Model {
    protected ?string $table = 'materials';

    public int $id;
    public int $lesson_id;
    public string $filename;
    public string $file_path;
    public ?string $file_type = null;
    public ?string $uploaded_at = null;

    public static function getFileType($filename): string
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    public static function isAllowedType($fileType): bool
    {
        $allowedTypes = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt', 'zip', 'rar'];
        return in_array(strtolower($fileType), $allowedTypes);
    }
}