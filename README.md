# Personal Expense Tracker

A web-based Expense Tracker developed using Core PHP and MySQL.

## Technologies

* Core PHP
* MySQL
* HTML
* CSS
* Bootstrap
* JavaScript
* jQuery

## Features

* User Registration
* User Login and Logout
* Session Authentication
* Dashboard
* Income and Expense Management
* Add Transaction
* Edit Transaction
* Delete Transaction
* Search and Filter
* Monthly Income and Expense Summary
* Prepared Statements
* Password Hashing
* Form Validation

## CRUD Operations

* **Create** - Add Transaction
* **Read** - View Transactions
* **Update** - Edit Transaction
* **Delete** - Delete Transaction

## Project Structure

```text
expense_tracker/
├── asset/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── script.js
├── config/
│   └── database.php
├── includes/
│   ├── header.php
│   └── footer.php
├── add_transaction.php
├── dashboard.php
├── database.sql
├── delete_transaction.php
├── edit_transaction.php
├── index.php
├── login.php
├── logout.php
├── register.php
└── transactions.php
```

## How to Run

1. Install XAMPP.
2. Start Apache and MySQL.
3. Copy the project into the `htdocs` folder.
4. Create a database named `expense_tracker`.
5. Import `database.sql` into phpMyAdmin.
6. Update the database configuration if required.
7. Open the project in the browser.

## Security

* Passwords are securely hashed using `password_hash()`.
* Prepared statements are used for database queries.
* Session authentication is implemented.
* User transactions are restricted using `user_id`.
* Output is escaped using `htmlspecialchars()`.

## Developer

**Soundarya**
