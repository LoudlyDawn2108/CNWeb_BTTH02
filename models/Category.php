<?php
namespace Models;
use Model;

require_once __DIR__ . '/../lib/Model.php';

class Category extends Model {
    const TABLE = 'categories';
    const ID = 'id';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const CREATED_AT = 'created_at';

    protected ?string $table = self::TABLE;

    public int $id;
    public string $name;
    public ?string $description = null;
    public ?string $created_at = null;
    
    // Virtual property for view
    public int $course_count = 0;
}