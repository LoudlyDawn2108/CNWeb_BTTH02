<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/QueryBuilder.php';

abstract class Model {
    protected ?string $table = null;
    protected string $primaryKey = 'id';
    protected array $attributes = [];
    protected static ?PDO $pdo = null;

    public function __construct(array $attributes = []) {
        $this->fill($attributes);
    }
    
    /**
     * Get the database connection (Singleton-ish)
     */
    protected static function getConnection(): PDO {
        if (!self::$pdo) {
            $database = new Database();
            self::$pdo = $database->getConnection();
        }
        return self::$pdo;
    }

    /**
     * Fill attributes
     */
    public function fill(array $attributes): void {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }
    }

    /**
     * Magic getter for attributes
     */
    public function __get(string $name): mixed {
        return $this->attributes[$name] ?? null;
    }

    /**
     * Magic setter for attributes
     */
    public function __set(string $name, mixed $value): void {
        $this->attributes[$name] = $value;
    }

    /**
     * Begin a new query
     */
    public static function query(): QueryBuilder
    {
        $instance = new static();
        return new QueryBuilder(self::getConnection())
            ->table($instance->getTable())
            ->setModel(static::class);
    }

    /**
     * Get all records
     */
    public static function all(): array
    {
        return static::query()->get();
    }

    /**
     * Find record by ID
     */
    public static function find(mixed $id): ?static {
        $instance = new static();
        return static::query()
            ->where($instance->primaryKey, $id)
            ->first();
    }

    /**
     * Get the table name
     */
    public function getTable(): string
    {
        if ($this->table) {
            return $this->table;
        }
        // Infer table name from class name (e.g. User -> users)
        $class = new ReflectionClass($this)->getShortName();
        return strtolower($class) . 's';
    }

    /**
     * Create a new record
     */
    public static function create(array $attributes): static {
        $instance = new static($attributes);
        $instance->save();
        return $instance;
    }
    
    /**
     * Forward static calls to QueryBuilder
     */
    public static function __callStatic(string $method, array $parameters): mixed {
        return static::query()->$method(...$parameters);
    }

    /**
     * Save the model (insert or update)
     */
    public function save(): bool
    {
        $builder = new QueryBuilder(self::getConnection())
            ->table($this->getTable());
            
        if (isset($this->attributes[$this->primaryKey])) {
            // Update
            $builder->where($this->primaryKey, $this->attributes[$this->primaryKey])
                    ->update($this->attributes);
        } else {
            // Insert
            $id = $builder->insert($this->attributes);
            $this->attributes[$this->primaryKey] = $id;
        }
        
        return true;
    }

    /**
     * Delete the model
     */
    public function delete(): bool
    {
        if (!isset($this->attributes[$this->primaryKey])) {
            return false;
        }
        
        return new QueryBuilder(self::getConnection())
            ->table($this->getTable())
            ->where($this->primaryKey, $this->attributes[$this->primaryKey])
            ->delete();
    }
    
    /**
     * Convert to array
     */
    public function toArray(): array {
        return $this->attributes;
    }
}
