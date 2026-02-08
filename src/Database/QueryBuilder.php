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
        // Reset delle proprietà per evitare "residui" da query precedenti
        $this->where = [];
        $this->bindings = [];
        $this->columns = ['*'];
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

    /**
     * Recupera tutti i risultati
     */
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

    /**
     * Recupera un singolo record per ID (Metodo Helper comodissimo)
     */
    public function find(int|string $id)
    {
        $sql = "SELECT " . implode(', ', $this->columns) . " FROM {$this->table} WHERE id = ? LIMIT 1";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([$id]);

        return $statement->fetch();
    }

    public function insert(array $data): bool
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";

        return $this->pdo->prepare($sql)->execute(array_values($data));
    }
}