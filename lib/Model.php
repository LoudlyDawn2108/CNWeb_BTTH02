<?php

namespace lib;

use Database;
use PDO;
use QueryBuilder;
use ReflectionClass;

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
            if (property_exists($this, $key)) {
                $this->$key = $value;
            } else {
                $this->attributes[$key] = $value;
            }
        }
    }

    /**
     * Magic getter for attributes
     */
    public function __get(string $name): mixed {
        // Prioritize explicit property if it exists (though usually handled by PHP access)
        // If the property is protected/private, this might run? 
        // But for our use case, we look in attributes array.
        return $this->attributes[$name] ?? null;
    }

    /**
     * Magic setter for attributes
     */
    public function __set(string $name, mixed $value): void {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        } else {
            $this->attributes[$name] = $value;
        }
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
        $data = $this->getAttributesForSave();
        $builder = new QueryBuilder(self::getConnection())
            ->table($this->getTable());
            
        // Check if primary key has a value (non-null)
        // We check both public property and attributes array
        $pkValue = null;
        if (property_exists($this, $this->primaryKey) && isset($this->{$this->primaryKey})) {
            $pkValue = $this->{$this->primaryKey};
        } elseif (isset($this->attributes[$this->primaryKey])) {
            $pkValue = $this->attributes[$this->primaryKey];
        }

        if ($pkValue) {
            // Update
            $builder->where($this->primaryKey, $pkValue)
                    ->update($data);
        } else {
            // Insert
            $id = $builder->insert($data);
            
            // Assign ID back to model
            if (property_exists($this, $this->primaryKey)) {
                $this->{$this->primaryKey} = $id;
            } else {
                $this->attributes[$this->primaryKey] = $id;
            }
        }
        
        return true;
    }

    /**
     * Delete the model
     */
    public function delete(): bool
    {
        $pkValue = null;
        if (property_exists($this, $this->primaryKey) && isset($this->{$this->primaryKey})) {
            $pkValue = $this->{$this->primaryKey};
        } elseif (isset($this->attributes[$this->primaryKey])) {
            $pkValue = $this->attributes[$this->primaryKey];
        }

        if (!$pkValue) {
            return false;
        }
        
        return new QueryBuilder(self::getConnection())
            ->table($this->getTable())
            ->where($this->primaryKey, $pkValue)
            ->delete();
    }
    
    /**
     * Convert to array
     */
    public function toArray(): array {
        return $this->getAttributesForSave();
    }

    /**
     * Helper to get all attributes (public properties + attributes array)
     */
    protected function getAttributesForSave(): array {
        $data = $this->attributes;
        
        $reflect = new ReflectionClass($this);
        $props = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
        
        foreach ($props as $prop) {
            if (!$prop->isStatic()) {
                $name = $prop->getName();
                // Skip uninitialized properties
                if (isset($this->$name)) {
                     $data[$name] = $this->$name;
                }
            }
        }
        
        return $data;
    }
}
