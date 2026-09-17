<?php

class Transaction
{
    private mysqli $connection;

    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }

    public function addTransaction(
        int $userId,
        string $type,
        string $category,
        float $amount,
        string $description,
        string $transactionDate
    ): bool {
        $query = 'INSERT INTO transactions
            (user_id, type, category, amount, description, transaction_date)
            VALUES (?, ?, ?, ?, ?, ?)';
        $statement = $this->connection->prepare($query);
        $statement->bind_param(
            'issdss',
            $userId,
            $type,
            $category,
            $amount,
            $description,
            $transactionDate
        );

        return $statement->execute();
    }

    public function updateTransaction(
        int $id,
        int $userId,
        string $type,
        string $category,
        float $amount,
        string $description,
        string $transactionDate
    ): bool {
        $query = 'UPDATE transactions
            SET type = ?, category = ?, amount = ?, description = ?, transaction_date = ?
            WHERE id = ? AND user_id = ?';
        $statement = $this->connection->prepare($query);
        $statement->bind_param(
            'ssdssii',
            $type,
            $category,
            $amount,
            $description,
            $transactionDate,
            $id,
            $userId
        );

        return $statement->execute();
    }

    public function deleteTransaction(int $id, int $userId): bool
    {
        $query = 'DELETE FROM transactions WHERE id = ? AND user_id = ?';
        $statement = $this->connection->prepare($query);
        $statement->bind_param('ii', $id, $userId);

        return $statement->execute();
    }

    public function getTransactionById(int $id, int $userId): ?array
    {
        $query = 'SELECT id, type, category, amount, description, transaction_date
            FROM transactions WHERE id = ? AND user_id = ? LIMIT 1';
        $statement = $this->connection->prepare($query);
        $statement->bind_param('ii', $id, $userId);
        $statement->execute();
        $transaction = $statement->get_result()->fetch_assoc();

        return $transaction ?: null;
    }

    public function getUserTransactions(
        int $userId,
        string $search = '',
        string $type = ''
    ): array {
        $query = 'SELECT id, type, category, amount, description, transaction_date
            FROM transactions WHERE user_id = ?';
        $types = 'i';
        $parameters = [$userId];

        if ($search !== '') {
            $query .= ' AND (category LIKE ? OR description LIKE ?)';
            $searchValue = '%' . $search . '%';
            $parameters[] = $searchValue;
            $parameters[] = $searchValue;
            $types .= 'ss';
        }

        if (Validator::transactionType($type)) {
            $query .= ' AND type = ?';
            $parameters[] = $type;
            $types .= 's';
        }

        $query .= ' ORDER BY transaction_date DESC, id DESC';
        $statement = $this->connection->prepare($query);
        $this->bindParameters($statement, $types, $parameters);
        $statement->execute();

        return $statement->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalIncome(int $userId): float
    {
        return $this->getTotalByType($userId, 'income');
    }

    public function getTotalExpense(int $userId): float
    {
        return $this->getTotalByType($userId, 'expense');
    }

    public function getBalance(int $userId): float
    {
        return $this->getTotalIncome($userId) - $this->getTotalExpense($userId);
    }

    public function getMonthlyTotal(int $userId, string $type, string $month): float
    {
        $query = "SELECT COALESCE(SUM(amount), 0) AS total
            FROM transactions
            WHERE user_id = ? AND type = ? AND DATE_FORMAT(transaction_date, '%Y-%m') = ?";
        $statement = $this->connection->prepare($query);
        $statement->bind_param('iss', $userId, $type, $month);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();

        return (float) $result['total'];
    }

    private function getTotalByType(int $userId, string $type): float
    {
        $query = 'SELECT COALESCE(SUM(amount), 0) AS total
            FROM transactions WHERE user_id = ? AND type = ?';
        $statement = $this->connection->prepare($query);
        $statement->bind_param('is', $userId, $type);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();

        return (float) $result['total'];
    }

    private function bindParameters(mysqli_stmt $statement, string $types, array &$parameters): void
    {
        $references = [];
        foreach ($parameters as $index => &$parameter) {
            $references[$index] = &$parameter;
        }

        $statement->bind_param($types, ...$references);
    }
}
