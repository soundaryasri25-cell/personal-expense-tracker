<?php

session_start();
require_once "config/database.php";

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Get Total Income
$income_query = "SELECT SUM(amount) AS total_income 
                 FROM transactions 
                 WHERE user_id = ? AND type = 'income'";

$stmt = mysqli_prepare($conn, $income_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$income_data = mysqli_fetch_assoc($result);

$total_income = $income_data['total_income'] ?? 0;


// Get Total Expense
$expense_query = "SELECT SUM(amount) AS total_expense 
                  FROM transactions 
                  WHERE user_id = ? AND type = 'expense'";

$stmt = mysqli_prepare($conn, $expense_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$expense_data = mysqli_fetch_assoc($result);

$total_expense = $expense_data['total_expense'] ?? 0;


// Calculate Balance
$balance = $total_income - $total_expense;

// Current month
$current_month = date('Y-m');

// Get current month income
$monthly_income_query = "SELECT SUM(amount) AS monthly_income
                         FROM transactions
                         WHERE user_id = ?
                         AND type = 'income'
                         AND DATE_FORMAT(transaction_date, '%Y-%m') = ?";

$stmt = mysqli_prepare($conn, $monthly_income_query);

mysqli_stmt_bind_param(
    $stmt,
    "is",
    $user_id,
    $current_month
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$monthly_income_data = mysqli_fetch_assoc($result);

$monthly_income = $monthly_income_data['monthly_income'] ?? 0;


// Get current month expense
$monthly_expense_query = "SELECT SUM(amount) AS monthly_expense
                          FROM transactions
                          WHERE user_id = ?
                          AND type = 'expense'
                          AND DATE_FORMAT(transaction_date, '%Y-%m') = ?";

$stmt = mysqli_prepare($conn, $monthly_expense_query);

mysqli_stmt_bind_param(
    $stmt,
    "is",
    $user_id,
    $current_month
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$monthly_expense_data = mysqli_fetch_assoc($result);

$monthly_expense = $monthly_expense_data['monthly_expense'] ?? 0;


// Monthly balance
$monthly_balance = $monthly_income - $monthly_expense;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - Expense Tracker</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <?php

    // Use common header
    require_once "includes/header.php";

    ?>
    <div class="container mt-4">

        <h2 class="mb-4">Dashboard</h2>


        <!-- Summary Cards -->
        <div class="row">

            <!-- Income -->
            <div class="col-md-4 mb-3">

                <div class="card shadow-sm">
                    <div class="card-body">

                        <h5 class="card-title">
                            Total Income
                        </h5>

                        <h3 class="text-success">
                            ₹ <?php echo number_format($total_income, 2); ?>
                        </h3>

                    </div>
                </div>

            </div>


            <!-- Expense -->
            <div class="col-md-4 mb-3">

                <div class="card shadow-sm">
                    <div class="card-body">

                        <h5 class="card-title">
                            Total Expense
                        </h5>

                        <h3 class="text-danger">
                            ₹ <?php echo number_format($total_expense, 2); ?>
                        </h3>

                    </div>
                </div>

            </div>


            <!-- Balance -->
            <div class="col-md-4 mb-3">

                <div class="card shadow-sm">
                    <div class="card-body">

                        <h5 class="card-title">
                            Balance
                        </h5>

                        <h3 class="text-primary">
                            ₹ <?php echo number_format($balance, 2); ?>
                        </h3>

                    </div>
                </div>

            </div>

        </div>
        <div class="row mt-3">

            <!-- Monthly Income -->

            <div class="col-md-4 mb-3">

                <div class="card shadow-sm">

                    <div class="card-body">

                        <h5 class="card-title">
                            This Month Income
                        </h5>

                        <h3 class="text-success">
                            ₹ <?php echo number_format($monthly_income, 2); ?>
                        </h3>

                    </div>

                </div>

            </div>


            <!-- Monthly Expense -->

            <div class="col-md-4 mb-3">

                <div class="card shadow-sm">

                    <div class="card-body">

                        <h5 class="card-title">
                            This Month Expense
                        </h5>

                        <h3 class="text-danger">
                            ₹ <?php echo number_format($monthly_expense, 2); ?>
                        </h3>

                    </div>

                </div>

            </div>


            <!-- Monthly Balance -->

            <div class="col-md-4 mb-3">

                <div class="card shadow-sm">

                    <div class="card-body">

                        <h5 class="card-title">
                            This Month Balance
                        </h5>

                        <h3 class="text-primary">
                            ₹ <?php echo number_format($monthly_balance, 2); ?>
                        </h3>

                    </div>

                </div>

            </div>

        </div>

        <!-- Action Buttons -->
        <div class="mt-4">

            <a href="add_transaction.php" class="btn btn-primary">
                + Add Transaction
            </a>

            <a href="transactions.php" class="btn btn-dark">
                View Transactions
            </a>

        </div>

    </div>

</body>

</html>