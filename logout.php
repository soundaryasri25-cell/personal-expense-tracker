<?php

session_start();
require_once 'config/database.php';

$auth = new User($db);
$auth->logout();

header('Location: login.php');
exit();
