<?php

session_start();
require_once 'config/database.php';

$auth = new User($db);
$message = '';

if (isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!Validator::required([$email, $password])) {
        $message = 'Email and password are required.';
    } elseif (!Validator::email($email)) {
        $message = 'Please enter a valid email address.';
    } else {
        $user = $auth->login($email, $password);

        if ($user === null) {
            $message = 'Invalid email or password.';
        } else {
            $auth->startSession($user);
            header('Location: dashboard.php');
            exit;
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
        rel="stylesheet">

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
                                    placeholder="Enter your email">

                            </div>

                            <div class="mb-3">

                                <label>Password</label>

                                <input
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    placeholder="Enter your password">

                            </div>

                            <button
                                type="submit"
                                name="login"
                                class="btn btn-primary w-100">
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