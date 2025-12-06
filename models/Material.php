<?php
require_once __DIR__ . '/../lib/Model.php';

class Material extends Model {
    protected ?string $table = 'materials';

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