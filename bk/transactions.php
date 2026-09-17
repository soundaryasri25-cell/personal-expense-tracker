<?php

session_start();
require_once "config/database.php";

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user's transactions
$query = "SELECT id, type, category, amount, description, transaction_date
          FROM transactions
          WHERE user_id = ?
          ORDER BY transaction_date DESC, id DESC";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Transactions - Expense Tracker</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body class="bg-light">

    <!-- Navbar -->

    <nav class="navbar navbar-dark bg-dark">

        <div class="container">

            <a
                href="dashboard.php"
                class="navbar-brand"
            >
                Expense Tracker
            </a>

            <div>

                <a
                    href="add_transaction.php"
                    class="btn btn-primary btn-sm me-2"
                >
                    + Add Transaction
                </a>

                <a
                    href="logout.php"
                    class="btn btn-danger btn-sm"
                >
                    Logout
                </a>

            </div>

        </div>

    </nav>


    <div class="container mt-4">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <h2>
                Transactions
            </h2>

            <a
                href="dashboard.php"
                class="btn btn-secondary"
            >
                Dashboard
            </a>

        </div>


        <div class="card shadow-sm">

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover">

                        <thead class="table-dark">

                            <tr>

                                <th>#</th>

                                <th>Type</th>

                                <th>Category</th>

                                <th>Amount</th>

                                <th>Description</th>

                                <th>Date</th>

                                <th>Action</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php

                            if (mysqli_num_rows($result) > 0) {

                                $count = 1;

                                while ($row = mysqli_fetch_assoc($result)) {

                            ?>

                                    <tr>

                                        <td>
                                            <?php echo $count++; ?>
                                        </td>

                                        <td>

                                            <?php if ($row['type'] == 'income') { ?>

                                                <span class="badge bg-success">
                                                    Income
                                                </span>

                                            <?php } else { ?>

                                                <span class="badge bg-danger">
                                                    Expense
                                                </span>

                                            <?php } ?>

                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($row['category']); ?>
                                        </td>

                                        <td>
                                            ₹ <?php echo number_format($row['amount'], 2); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($row['description']); ?>
                                        </td>

                                        <td>
                                            <?php echo date('d-m-Y', strtotime($row['transaction_date'])); ?>
                                        </td>

                                        <td>

                                            <a
                                                href="edit_transaction.php?id=<?php echo $row['id']; ?>"
                                                class="btn btn-warning btn-sm"
                                            >
                                                Edit
                                            </a>

                                            <a
                                                href="delete_transaction.php?id=<?php echo $row['id']; ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this transaction?');"
                                            >
                                                Delete
                                            </a>

                                        </td>

                                    </tr>

                            <?php

                                }

                            } else {

                            ?>

                                <tr>

                                    <td
                                        colspan="7"
                                        class="text-center"
                                    >
                                        No transactions found.
                                    </td>

                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</body>

</html>