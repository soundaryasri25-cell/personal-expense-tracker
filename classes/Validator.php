<?php

class Validator
{
    public static function required(array $values): bool
    {
        foreach ($values as $value) {
            if ($value === null || trim((string) $value) === '') {
                return false;
            }
        }

        return true;
    }

    public static function email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function password(string $password): bool
    {
        return strlen($password) >= 6;
    }

    public static function amount($amount): bool
    {
        return is_numeric($amount) && (float) $amount > 0;
    }

    public static function transactionType(string $type): bool
    {
        return in_array($type, ['income', 'expense'], true);
    }

    public static function date(string $date): bool
    {
        $parsedDate = DateTime::createFromFormat('Y-m-d', $date);

        return $parsedDate !== false && $parsedDate->format('Y-m-d') === $date;
    }
}
