<?php

session_start();
require_once 'config/database.php';

$auth = new User($conn);
$auth->logout();

header('Location: login.php');
exit();
