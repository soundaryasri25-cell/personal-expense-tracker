<?php

session_start();
require_once "config/database.php";

$message = "";

if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($email == "" || $password == "") {

        $message = "Email and password are required.";

    } else {

        $sql = "SELECT id, name, email, password
                FROM users
                WHERE email = ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "s", $email);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {

            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];

                header("Location: dashboard.php");
                exit;

            } else {

                $message = "Invalid email or password.";
            }

        } else {

            $message = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>

    <title>Login - Expense Tracker</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-5">

            <div class="card shadow">

                <div class="card-body">

                    <h3 class="text-center mb-4">
                        Expense Tracker
                    </h3>

                    <h5 class="text-center mb-4">
                        Login
                    </h5>

                    <?php if ($message != "") { ?>

                        <div class="alert alert-danger">
                            <?php echo $message; ?>
                        </div>

                    <?php } ?>

                    <form method="POST">

                        <div class="mb-3">

                            <label>Email</label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                placeholder="Enter your email"
                            >

                        </div>

                        <div class="mb-3">

                            <label>Password</label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                placeholder="Enter your password"
                            >

                        </div>

                        <button
                            type="submit"
                            name="login"
                            class="btn btn-primary w-100"
                        >
                            Login
                        </button>

                    </form>

                    <p class="text-center mt-3">

                        Don't have an account?

                        <a href="register.php">
                            Register
                        </a>

                    </p>

                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>