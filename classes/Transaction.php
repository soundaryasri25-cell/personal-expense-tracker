<?php

class Transaction
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
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
        return $this->database->execute($query, 'issdss', [
            $userId,
            $type,
            $category,
            $amount,
            $description,
            $transactionDate
        ]);
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
        return $this->database->execute($query, 'ssdssii', [
            $type,
            $category,
            $amount,
            $description,
            $transactionDate,
            $id,
            $userId
        ]);
    }

    public function deleteTransaction(int $id, int $userId): bool
    {
        $query = 'DELETE FROM transactions WHERE id = ? AND user_id = ?';
        return $this->database->execute($query, 'ii', [$id, $userId]);
    }

    public function getTransactionById(int $id, int $userId): ?array
    {
        $query = 'SELECT id, type, category, amount, description, transaction_date
            FROM transactions WHERE id = ? AND user_id = ? LIMIT 1';
        $transactions = $this->database->find_by_sql($query, 'ii', [$id, $userId]);
        $transaction = $transactions[0] ?? null;

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
        return $this->database->find_by_sql($query, $types, $parameters);
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
        $rows = $this->database->find_by_sql($query, 'iss', [$userId, $type, $month]);
        $result = $rows[0] ?? ['total' => 0];

        return (float) $result['total'];
    }

    private function getTotalByType(int $userId, string $type): float
    {
        $query = 'SELECT COALESCE(SUM(amount), 0) AS total
            FROM transactions WHERE user_id = ? AND type = ?';
        $rows = $this->database->find_by_sql($query, 'is', [$userId, $type]);
        $result = $rows[0] ?? ['total' => 0];

        return (float) $result['total'];
    }
}
