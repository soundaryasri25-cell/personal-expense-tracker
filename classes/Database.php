<?php

class Database
{
    private string $host;
    private string $username;
    private string $password;
    private string $database;
    private ?mysqli $connection = null;

    public function __construct(
        string $host,
        string $username,
        string $password,
        string $database
    ) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
    }

    public function getConnection(): mysqli
    {
        if ($this->connection === null) {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $this->connection = new mysqli(
                $this->host,
                $this->username,
                $this->password,
                $this->database
            );
            $this->connection->set_charset('utf8mb4');
        }

        return $this->connection;
    }

    public function find_by_sql(
        string $sql,
        string $types = '',
        array $parameters = []
    ): array {
        $statement = $this->prepareStatement($sql, $types, $parameters);
        $statement->execute();

        return $statement->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function find_by_id(
        string $table,
        int $id,
        string $columns = '*',
        string $idColumn = 'id'
    ): ?array {
        $this->validateIdentifier($table);
        $this->validateIdentifier($idColumn);
        $this->validateColumns($columns);

        $rows = $this->find_by_sql(
            "SELECT {$columns} FROM {$table} WHERE {$idColumn} = ? LIMIT 1",
            'i',
            [$id]
        );

        return $rows[0] ?? null;
    }

    public function execute(
        string $sql,
        string $types = '',
        array $parameters = []
    ): bool {
        $statement = $this->prepareStatement($sql, $types, $parameters);

        return $statement->execute();
    }

    private function prepareStatement(
        string $sql,
        string $types,
        array &$parameters
    ): mysqli_stmt {
        $statement = $this->getConnection()->prepare($sql);

        if ($types !== '') {
            $references = [];
            foreach ($parameters as $index => &$parameter) {
                $references[$index] = &$parameter;
            }
            $statement->bind_param($types, ...$references);
        }

        return $statement;
    }

    private function validateIdentifier(string $identifier): void
    {
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $identifier)) {
            throw new InvalidArgumentException('Invalid database identifier.');
        }
    }

    private function validateColumns(string $columns): void
    {
        if ($columns === '*') {
            return;
        }

        foreach (explode(',', $columns) as $column) {
            $this->validateIdentifier(trim($column));
        }
    }
}
