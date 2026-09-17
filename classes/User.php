<?php

class User
{
    private mysqli $connection;

    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }

    public function register(string $name, string $email, string $password): bool
    {
        $query = 'INSERT INTO users (name, email, password) VALUES (?, ?, ?)';
        $statement = $this->connection->prepare($query);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $statement->bind_param('sss', $name, $email, $hashedPassword);

        return $statement->execute();
    }

    public function emailExists(string $email): bool
    {
        $query = 'SELECT id FROM users WHERE email = ? LIMIT 1';
        $statement = $this->connection->prepare($query);
        $statement->bind_param('s', $email);
        $statement->execute();
        $result = $statement->get_result();

        return $result->num_rows === 1;
    }

    public function login(string $email, string $password): ?array
    {
        $query = 'SELECT id, name, email, password FROM users WHERE email = ? LIMIT 1';
        $statement = $this->connection->prepare($query);
        $statement->bind_param('s', $email);
        $statement->execute();
        $user = $statement->get_result()->fetch_assoc();

        if (!$user || !password_verify($password, $user['password'])) {
            return null;
        }

        return $user;
    }

    public function startSession(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_name'] = $user['name'];
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id']);
    }

    public function requireLogin(): void
    {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $parameters = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $parameters['path'],
                $parameters['domain'],
                $parameters['secure'],
                $parameters['httponly']
            );
        }

        session_destroy();
    }
}
