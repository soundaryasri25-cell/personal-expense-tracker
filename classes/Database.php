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
}
