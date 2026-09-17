<?php

session_start();
require_once "config/database.php";

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$type = isset($_GET['type']) ? $_GET['type'] : "";

$query = "SELECT id, type, category, amount, description, transaction_date
          FROM transactions
          WHERE user_id = ?";

$params = [$user_id];
$types = "i";


// Search filter
if (!empty($search)) {

    $query .= " AND (category LIKE ? OR description LIKE ?)";

    $search_value = "%" . $search . "%";

    $params[] = $search_value;
    $params[] = $search_value;

    $types .= "ss";
}


// Income / Expense filter
if ($type == "income" || $type == "expense") {

    $query .= " AND type = ?";

    $params[] = $type;

    $types .= "s";
}


$query .= " ORDER BY transaction_date DESC, id DESC";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param($stmt, $types, ...$params);

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
        rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body class="bg-light">

    <?php

    // Use common header
    require_once "includes/header.php";

    ?>

    <?php
    if (isset($_SESSION['message'])) {
        $msg = $_SESSION['message'];
        echo "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire({ icon: 'success', title: 'Success', text: " . json_encode($msg) . " }); });</script>";
        unset($_SESSION['message']);
    }

    if (isset($_SESSION['error'])) {
        $err = $_SESSION['error'];
        echo "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire({ icon: 'error', title: 'Error', text: " . json_encode($err) . " }); });</script>";
        unset($_SESSION['error']);
    }
    ?>


    <div class="container mt-4">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <h2>
                Transactions
            </h2>

            <a
                href="dashboard.php"
                class="btn btn-secondary">
                Dashboard
            </a>

        </div>


        <!-- Search & Filter -->

        <div class="card shadow-sm mb-4">

            <div class="card-body">

                <form method="GET">

                    <div class="row">

                        <!-- Search -->

                        <div class="col-md-5 mb-2">

                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Search category or description"
                                value="<?php echo htmlspecialchars($search); ?>">

                        </div>


                        <!-- Type -->

                        <div class="col-md-3 mb-2">

                            <select
                                name="type"
                                class="form-select">

                                <option value="">
                                    All Transactions
                                </option>

                                <option
                                    value="income"
                                    <?php
                                    if ($type == "income") {
                                        echo "selected";
                                    }
                                    ?>>
                                    Income
                                </option>

                                <option
                                    value="expense"
                                    <?php
                                    if ($type == "expense") {
                                        echo "selected";
                                    }
                                    ?>>
                                    Expense
                                </option>

                            </select>

                        </div>


                        <!-- Search button -->

                        <div class="col-md-2 mb-2">

                            <button
                                type="submit"
                                class="btn btn-primary w-100">
                                Search
                            </button>

                        </div>


                        <!-- Reset -->

                        <div class="col-md-2 mb-2">

                            <a
                                href="transactions.php"
                                class="btn btn-secondary w-100">
                                Reset
                            </a>

                        </div>

                    </div>

                </form>

            </div>

        </div>


        <!-- Transaction Table -->

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
                                            <?php
                                            echo date(
                                                'd-m-Y',
                                                strtotime($row['transaction_date'])
                                            );
                                            ?>
                                        </td>

                                        <td>

                                            <a
                                                href="edit_transaction.php?id=<?php echo $row['id']; ?>"
                                                class="btn btn-warning btn-sm">
                                                Edit
                                            </a>

                                            <a
                                                href="delete_transaction.php?id=<?php echo $row['id']; ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this transaction?');">
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
                                        class="text-center">
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