<?php

session_start();
require_once "config/database.php";

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check transaction ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: transactions.php");
    exit();
}

$transaction_id = $_GET['id'];

// Delete only the logged-in user's transaction
$query = "DELETE FROM transactions
          WHERE id = ? AND user_id = ?";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $transaction_id,
    $user_id
);

mysqli_stmt_execute($stmt);

// Go back to transactions page
header("Location: transactions.php");
exit();

?>