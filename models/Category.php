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
     * Lấy category theo ID
     */
    public function getById(int $id): Option {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result ? Option::some($result) : Option::none();
    }

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

    /**
     * Lấy category theo slug
     */
    public function getBySlug(string $slug): Option {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ?");
        $stmt->execute([$slug]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result ? Option::some($result) : Option::none();
    }

    /**
     * Tạo category mới
     */
    public function createCategory(array $data): Option {
        try {
            $pdo = self::getConnection();
            $stmt = $pdo->prepare("
                INSERT INTO categories (name, description, slug, created_at)
                VALUES (?, ?, ?, NOW())
            ");

            $stmt->execute([
                $data['name'],
                $data['description'] ?? '',
                $data['slug'] ?? $this->generateSlug($data['name'])
            ]);

            return Option::some($pdo->lastInsertId());
        } catch (\Exception $e) {
            return Option::none();
        }
    }

    /**
     * Cập nhật category
     */
    public function updateCategory(int $id, array $data): Option {
        try {
            $fields = [];
            $values = [];

            foreach ($data as $key => $value) {
                if (in_array($key, $this->fillable)) {
                    $fields[] = "$key = ?";
                    $values[] = $value;
                }
            }

            if (empty($fields)) {
                return Option::none();
            }

            $values[] = $id;
            $sql = "UPDATE categories SET " . implode(', ', $fields) . " WHERE id = ?";

            $pdo = self::getConnection();
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute($values);

            return $result ? Option::some(true) : Option::none();
        } catch (\Exception $e) {
            return Option::none();
        }
    }

    /**
     * Xóa category
     */
    public function deleteCategory(int $id): bool {
        try {
            $pdo = self::getConnection();
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Lấy categories có courses
     */
    public function getWithCourses(): Collection {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("
            SELECT c.*,
                   COUNT(co.id) as course_count
            FROM categories c
            INNER JOIN courses co ON c.id = co.category_id
            GROUP BY c.id
            HAVING course_count > 0
            ORDER BY c.name ASC
        ");
        $stmt->execute();
        return Collection::make($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    /**
     * Tạo slug từ tên
     */
    private function generateSlug(string $name): string {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        return $slug;
    }

    /**
     * Kiểm tra slug đã tồn tại
     */
    public function slugExists(string $slug, ?int $exceptId = null): bool {
        $pdo = self::getConnection();
        $sql = "SELECT COUNT(*) FROM categories WHERE slug = ?";
        $params = [$slug];

        if ($exceptId) {
            $sql .= " AND id != ?";
            $params[] = $exceptId;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchColumn() > 0;
    }
}