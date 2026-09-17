<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Expense Tracker</title>

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">

    <div class="container">

        <a
            href="dashboard.php"
            class="navbar-brand"
        >
            Expense Tracker
        </a>

        <?php if (isset($_SESSION['user_id'])) { ?>

            <div>

                <span class="text-white me-3">
                    Welcome,
                    <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                </span>

                <a
                    href="logout.php"
                    class="btn btn-danger btn-sm"
                >
                    Logout
                </a>

            </div>

        <?php } ?>

    </div>

</nav>