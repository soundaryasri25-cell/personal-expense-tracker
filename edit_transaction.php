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

$error = "";
$success = "";

// Get transaction details
$query = "SELECT * FROM transactions
          WHERE id = ? AND user_id = ?";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $transaction_id,
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$transaction = mysqli_fetch_assoc($result);

if (!$transaction) {
    header("Location: transactions.php");
    exit();
}


// Update transaction
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $type = $_POST['type'];
    $category = trim($_POST['category']);
    $amount = $_POST['amount'];
    $description = trim($_POST['description']);
    $transaction_date = $_POST['transaction_date'];

    if (
        empty($type) ||
        empty($category) ||
        empty($amount) ||
        empty($transaction_date)
    ) {

        $error = "Please fill all required fields.";

    } elseif ($amount <= 0) {

        $error = "Amount must be greater than 0.";

    } else {

        $update_query = "UPDATE transactions
                         SET type = ?,
                             category = ?,
                             amount = ?,
                             description = ?,
                             transaction_date = ?
                         WHERE id = ? AND user_id = ?";

        $stmt = mysqli_prepare($conn, $update_query);

        mysqli_stmt_bind_param(
            $stmt,
            "ssdssii",
            $type,
            $category,
            $amount,
            $description,
            $transaction_date,
            $transaction_id,
            $user_id
        );

        if (mysqli_stmt_execute($stmt)) {

            header("Location: transactions.php");
            exit();

        } else {

            $error = "Failed to update transaction.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Transaction</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body class="bg-light">

    <div class="container mt-5">

        <div class="row justify-content-center">

            <div class="col-md-7">

                <div class="card shadow">

                    <div class="card-header bg-dark text-white">

                        <h4 class="mb-0">
                            Edit Transaction
                        </h4>

                    </div>

                    <div class="card-body">

                        <?php if (!empty($error)) { ?>

                            <div class="alert alert-danger">
                                <?php echo $error; ?>
                            </div>

                        <?php } ?>


                        <form method="POST">

                            <!-- Type -->

                            <div class="mb-3">

                                <label class="form-label">
                                    Transaction Type
                                </label>

                                <select
                                    name="type"
                                    class="form-select"
                                    required
                                >

                                    <option
                                        value="income"
                                        <?php
                                        if ($transaction['type'] == 'income') {
                                            echo 'selected';
                                        }
                                        ?>
                                    >
                                        Income
                                    </option>

                                    <option
                                        value="expense"
                                        <?php
                                        if ($transaction['type'] == 'expense') {
                                            echo 'selected';
                                        }
                                        ?>
                                    >
                                        Expense
                                    </option>

                                </select>

                            </div>


                            <!-- Category -->

                            <div class="mb-3">

                                <label class="form-label">
                                    Category
                                </label>

                                <input
                                    type="text"
                                    name="category"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($transaction['category']); ?>"
                                    required
                                >

                            </div>


                            <!-- Amount -->

                            <div class="mb-3">

                                <label class="form-label">
                                    Amount
                                </label>

                                <input
                                    type="number"
                                    name="amount"
                                    class="form-control"
                                    step="0.01"
                                    min="0.01"
                                    value="<?php echo $transaction['amount']; ?>"
                                    required
                                >

                            </div>


                            <!-- Description -->

                            <div class="mb-3">

                                <label class="form-label">
                                    Description
                                </label>

                                <textarea
                                    name="description"
                                    class="form-control"
                                    rows="3"
                                ><?php echo htmlspecialchars($transaction['description']); ?></textarea>

                            </div>


                            <!-- Date -->

                            <div class="mb-3">

                                <label class="form-label">
                                    Transaction Date
                                </label>

                                <input
                                    type="date"
                                    name="transaction_date"
                                    class="form-control"
                                    value="<?php echo $transaction['transaction_date']; ?>"
                                    required
                                >

                            </div>


                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                Update Transaction
                            </button>

                            <a
                                href="transactions.php"
                                class="btn btn-secondary"
                            >
                                Cancel
                            </a>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>
<?php require_once "includes/footer.php"; ?>

</body>

</html>