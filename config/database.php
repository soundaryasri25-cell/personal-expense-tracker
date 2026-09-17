<?php

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Helper.php';
require_once __DIR__ . '/../classes/Validator.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Transaction.php';

$database = new Database(
    'localhost',
    'root',
    '',
    'expense_tracker'
);

$conn = $database->getConnection();
