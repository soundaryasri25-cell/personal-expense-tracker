<?php

class User
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function register(string $name, string $email, string $password): bool
    {
        $query = 'INSERT INTO users (name, email, password) VALUES (?, ?, ?)';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        return $this->database->execute(
            $query,
            'sss',
            [$name, $email, $hashedPassword]
        );
    }

    public function emailExists(string $email): bool
    {
        $query = 'SELECT id FROM users WHERE email = ? LIMIT 1';
        $result = $this->database->find_by_sql($query, 's', [$email]);

        return count($result) === 1;
    }

    public function login(string $email, string $password): ?array
    {
        $query = 'SELECT id, name, email, password FROM users WHERE email = ? LIMIT 1';
        $users = $this->database->find_by_sql($query, 's', [$email]);
        $user = $users[0] ?? null;

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
