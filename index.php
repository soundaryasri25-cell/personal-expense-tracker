<?php

session_start();
require_once 'config/database.php';

$auth = new User($db);

if ($auth->isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

header("Location: login.php");
exit;
