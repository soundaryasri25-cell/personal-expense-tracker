<?php

require_once "config/database.php";
session_start();

$message = "";

if (isset($_POST['register'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($name == "" || $email == "" || $password == "") {
        $message = "All fields are required.";
    } else {

        // Check email already exists
        $check_sql = "SELECT id FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $check_sql);

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {

            $message = "Email already exists.";
        } else {

            // Hash password
            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $insert_sql = "INSERT INTO users (name, email, password)
                           VALUES (?, ?, ?)";

            $stmt = mysqli_prepare($conn, $insert_sql);

            mysqli_stmt_bind_param(
                $stmt,
                "sss",
                $name,
                $email,
                $hashed_password
            );

            if (mysqli_stmt_execute($stmt)) {

                header("Location: login.php");
                exit;
            } else {

                $message = "Registration failed.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Register - Expense Tracker</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>

<body>

    <div class="container mt-5">

        <div class="row justify-content-center">

            <div class="col-md-5">

                <div class="card shadow">

                    <div class="card-body">

                        <h3 class="text-center mb-4">
                            Create Account
                        </h3>

                        <?php if ($message != "") { ?>

                            <div class="alert alert-danger">
                                <?php echo $message; ?>
                            </div>

                        <?php } ?>

                        <form method="POST">

                            <div class="mb-3">
                                <label>Name</label>

                                <input type="text"
                                    name="name"
                                    class="form-control">
                            </div>

                            <div class="mb-3">
                                <label>Email</label>

                                <input type="email"
                                    name="email"
                                    class="form-control">
                            </div>

                            <div class="mb-3">
                                <label>Password</label>

                                <input type="password"
                                    name="password"
                                    class="form-control">
                            </div>

                            <button type="submit"
                                name="register"
                                class="btn btn-primary w-100">

                                Register

                            </button>

                        </form>

                        <p class="text-center mt-3">
                            Already have an account?
                            <a href="login.php">Login</a>
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</body>

</html>