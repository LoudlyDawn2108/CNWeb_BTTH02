<?php
namespace Models;
use Model;

require_once __DIR__ . '/../lib/Model.php';

class CategoryTable {
    public function __toString(): string {
        return 'categories';
    }
    public string $ID = 'categories.id';
    public string $NAME = 'categories.name';
    public string $DESCRIPTION = 'categories.description';
    public string $CREATED_AT = 'categories.created_at';
}

class Category extends Model {
    protected ?string $table = 'categories';

    public int $id;
    public string $name;
    public ?string $description = null;
    public ?string $created_at = null;
    
    // Virtual property for view
    public int $course_count = 0;
}