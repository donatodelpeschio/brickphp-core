<?php

namespace BrickPHP\Database;

use PDO;

class QueryBuilder
{
    protected string $table;
    protected array $where = [];
    protected array $bindings = [];
    protected array $columns = ['*'];

    public function __construct(protected PDO $pdo) {}

    public function table(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function select(array $columns = ['*']): self
    {
        $this->columns = $columns;
        return $this;
    }

    public function where(string $column, string $operator, mixed $value): self
    {
        $this->where[] = "$column $operator ?";
        $this->bindings[] = $value;
        return $this;
    }

    public function get(): array
    {
        $sql = "SELECT " . implode(', ', $this->columns) . " FROM {$this->table}";

        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }

        $statement = $this->pdo->prepare($sql);
        $statement->execute($this->bindings);

        return $statement->fetchAll();
    }

    public function insert(array $data): bool
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";

        return $this->pdo->prepare($sql)->execute(array_values($data));
    }
}