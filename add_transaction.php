<?php

session_start();
require_once "config/database.php";

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $type = $_POST['type'];
    $category = trim($_POST['category']);
    $amount = $_POST['amount'];
    $description = trim($_POST['description']);
    $transaction_date = $_POST['transaction_date'];

    // Validation
    if (
        empty($type) ||
        empty($category) ||
        empty($amount) ||
        empty($transaction_date)
    ) {
        $_SESSION['error'] = "Please fill all required fields.";
    } elseif ($amount <= 0) {
        $_SESSION['error'] = "Amount must be greater than 0.";
    } else {

        $query = "INSERT INTO transactions
                  (user_id, type, category, amount, description, transaction_date)
                  VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $query);

        mysqli_stmt_bind_param(
            $stmt,
            "issdss",
            $user_id,
            $type,
            $category,
            $amount,
            $description,
            $transaction_date
        );

        if (mysqli_stmt_execute($stmt)) {

            $_SESSION['message'] = "Transaction added successfully.";
        } else {

            $_SESSION['error'] = "Something went wrong. Please try again.";
        }
    }
    header("Location: transactions.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Transaction</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>

<body class="bg-light">

    <div class="container mt-5">

        <div class="row justify-content-center">

            <div class="col-md-7">

                <div class="card shadow">

                    <div class="card-header bg-dark text-white">

                        <h4 class="mb-0">
                            Add Transaction
                        </h4>

                    </div>

                    <div class="card-body">

                        <?php if (!empty($message)) { ?>

                            <div class="alert alert-success">
                                <?php echo $message; ?>
                            </div>

                        <?php } ?>


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
                                    required>

                                    <option value="">
                                        Select Type
                                    </option>

                                    <option value="income">
                                        Income
                                    </option>

                                    <option value="expense">
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
                                    placeholder="Example: Food, Salary, Travel"
                                    required>

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
                                    placeholder="Enter amount"
                                    required>

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
                                    placeholder="Enter description"></textarea>

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
                                    value="<?php echo date('Y-m-d'); ?>"
                                    required>

                            </div>


                            <button
                                type="submit"
                                class="btn btn-primary">
                                Add Transaction
                            </button>

                            <a
                                href="dashboard.php"
                                class="btn btn-secondary">
                                Back to Dashboard
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