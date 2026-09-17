<?php

session_start();
require_once 'config/database.php';

$auth = new User($db);
$auth->requireLogin();
$transactionService = new Transaction($db);
$userId = (int) $_SESSION['user_id'];

$total_income = $transactionService->getTotalIncome($userId);
$total_expense = $transactionService->getTotalExpense($userId);
$balance = $transactionService->getBalance($userId);
$currentMonth = date('Y-m');
$monthly_income = $transactionService->getMonthlyTotal($userId, 'income', $currentMonth);
$monthly_expense = $transactionService->getMonthlyTotal($userId, 'expense', $currentMonth);
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

    require_once "includes/header.php";

    ?>
    <div class="container mt-4">

        <h2 class="mb-4">Dashboard</h2>


        <div class="row">

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