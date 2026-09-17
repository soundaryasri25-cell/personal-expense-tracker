<?php

session_start();
require_once 'config/database.php';

$auth = new User($db);
$auth->requireLogin();
$transactionService = new Transaction($db);
$userId = (int) $_SESSION['user_id'];

if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
    header('Location: transactions.php');
    exit();
}

$transactionId = (int) $_GET['id'];
$transactionService->deleteTransaction($transactionId, $userId);

header('Location: transactions.php');
exit();
