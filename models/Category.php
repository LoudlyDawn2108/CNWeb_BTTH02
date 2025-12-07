<?php
namespace Models;
use Lib\Model;

use Functional\Collection;
use Functional\Option;
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

/**
 * @property int id
 * @property string name
 */

class Category extends Model {
    protected ?string $table = 'categories';

    public int $id;
    public string $name;
    public ?string $description = null;
    public ?string $created_at = null;

    // Virtual property for view
    public int $course_count = 0;
    protected array $fillable = [
        'name',
        'description',
        'slug'
    ];

    /**
     * Lấy tất cả categories
     */
    public function getAll(): Collection {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("
            SELECT c.*,
                   COUNT(co.id) as course_count
            FROM categories c
            LEFT JOIN courses co ON c.id = co.category_id
            GROUP BY c.id
            ORDER BY c.name ASC
        ");
        $stmt->execute();
        return Collection::make($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }
}