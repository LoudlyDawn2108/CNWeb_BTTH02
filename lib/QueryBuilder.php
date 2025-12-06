<?php /** @noinspection SqlWithoutWhere */

class QueryBuilder {
    protected PDO $pdo;
    protected string $table = '';
    protected ?string $modelClass = null;
    protected string $select = '*';
    protected array $joins = [];
    protected array $wheres = [];
    protected array $params = [];
    protected array $groups = [];
    protected array $orders = [];
    protected ?int $limit = null;
    protected ?int $offset = null;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Set the model class for hydration
     */
    public function setModel(string $class): self {
        $this->modelClass = $class;
        // If table is not set, try to infer it from model? 
        // Better to let the Model class set the table on the builder.
        return $this;
    }

    /**
     * Set the table for the query
     */
    public function table(string $table): self {
        $this->table = $table;
        return $this;
    }

    /**
     * Set columns to select
     */
    public function select(string|array $columns): self {
        $this->select = is_array($columns) ? implode(', ', $columns) : $columns;
        return $this;
    }

    /**
     * Add a JOIN clause
     */
    public function join(string $table, string $first, string $operator, string $second, string $type = 'INNER'): self {
        $this->joins[] = "{$type} JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }

    /**
     * Add a LEFT JOIN clause
     */
    public function leftJoin(string $table, string $first, string $operator, string $second): self {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }

    /**
     * Add a WHERE clause
     */
    public function where(string $column, mixed $operator, mixed $value = null): self {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        // Handle unique parameter names to avoid conflicts
        $paramName = ':where_' . count($this->params) . '_' . preg_replace('/[^a-zA-Z0-9]/', '', $column);
        $this->wheres[] = "{$column} {$operator} {$paramName}";
        $this->params[$paramName] = $value;
        
        return $this;
    }

    /**
     * Add a raw WHERE clause
     */
    public function whereRaw(string $sql, array $bindings = []): self {
        $this->wheres[] = $sql;
        $this->params = array_merge($this->params, $bindings);
        return $this;
    }

    /**
     * Add a GROUP BY clause
     */
    public function groupBy(string|array $groups): self {
        $groups = is_array($groups) ? $groups : func_get_args();
        $this->groups = array_merge($this->groups, $groups);
        return $this;
    }

    /**
     * Add an ORDER BY clause
     */
    public function orderBy(string $column, string $direction = 'ASC'): self {
        $this->orders[] = "{$column} {$direction}";
        return $this;
    }

    /**
     * Set LIMIT
     */
    public function limit(int $limit): self {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Set OFFSET
     */
    public function offset(int $offset): self {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Execute SELECT query and return all results
     */
    public function get(?string $className = null): array {
        $sql = $this->compileSelect();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->params);
        
        $fetchClass = $className ?? $this->modelClass;
        
        if ($fetchClass) {
            return $stmt->fetchAll(PDO::FETCH_CLASS, $fetchClass);
        }
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Execute SELECT query and return the first result
     */
    public function first(?string $className = null): mixed {
        $this->limit(1);
        $sql = $this->compileSelect();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->params);
        
        $fetchClass = $className ?? $this->modelClass;
        
        if ($fetchClass) {
            $stmt->setFetchMode(PDO::FETCH_CLASS, $fetchClass);
            return $stmt->fetch() ?: null;
        }
        
        return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    }

    /**
     * Execute INSERT query
     */
    public function insert(array $data): string|false {
        $columns = implode(', ', array_keys($data));
        $placeholders = [];
        $params = [];
        
        foreach ($data as $key => $value) {
            $paramName = ':ins_' . $key;
            $placeholders[] = $paramName;
            $params[$paramName] = $value;
        }
        
        $placeholdersStr = implode(', ', $placeholders);
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholdersStr})";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $this->pdo->lastInsertId();
    }

    /**
     * Execute UPDATE query
     */
    public function update(array $data): bool {
        $sets = [];
        $params = $this->params; // Keep existing where params
        
        foreach ($data as $key => $value) {
            $paramName = ':upd_' . $key;
            $sets[] = "{$key} = {$paramName}";
            $params[$paramName] = $value;
        }
        
        $setStr = implode(', ', $sets);
        $whereStr = $this->compileWheres();
        
        $sql = "UPDATE {$this->table} SET {$setStr} {$whereStr}";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return true;
    }

    /**
     * Execute DELETE query
     */
    public function delete(): bool {
        $whereStr = $this->compileWheres();
        $sql = "DELETE FROM {$this->table} {$whereStr}";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->params);
        return true;
    }
    
    /**
     * Count results
     */
    public function count(): int {
        $originalSelect = $this->select;
        $this->select = 'COUNT(*)';
        
        // Preserve and clear orders/limit/offset
        $tempOrders = $this->orders;
        $tempLimit = $this->limit;
        $tempOffset = $this->offset;
        
        $this->orders = [];
        $this->limit = null;
        $this->offset = null;

        $sql = $this->compileSelect();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->params);
        $count = (int)$stmt->fetchColumn();

        // Restore
        $this->select = $originalSelect;
        $this->orders = $tempOrders;
        $this->limit = $tempLimit;
        $this->offset = $tempOffset;

        return $count;
    }

    protected function compileSelect(): string {
        $sql = "SELECT {$this->select} FROM {$this->table}";
        
        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }
        
        $sql .= $this->compileWheres();

        if (!empty($this->groups)) {
            $sql .= ' GROUP BY ' . implode(', ', $this->groups);
        }
        
        if (!empty($this->orders)) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orders);
        }
        
        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }
        
        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }
        
        return $sql;
    }
    
    protected function compileWheres(): string {
        if (empty($this->wheres)) {
            return '';
        }
        return ' WHERE ' . implode(' AND ', $this->wheres);
    }
}
