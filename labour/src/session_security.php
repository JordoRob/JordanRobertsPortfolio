<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'] . "/labour/database-con.php"; // make connection to database (sets $pdo)

/**
 * Terminates the PHP script with a message and optional response code
 * @param string $message The message to return
 * @param int $response_code The HTTP response code to return (default 200: OK)
 */
function terminate($message, $response_code = 200) {
    $pdo = null; //close connection
    http_response_code($response_code);
    die($message);
}

/**
 * Checks if the user is logged in and, optionally, if the request method is correct. If not, terminates the script with an error message.
 * @param string $method The request method to check
 * @param array $variables The GET or POST variables to check
 */
function security_check_and_connect($method = null, $variables = null) {
    if (!isset($_SESSION['user'])) {
        header("Location: /labour/src/login.php");
    }

    // Check if recent password change (Variable set during login)
    if ($_SESSION['recent_password_reset'] == true) {
        // Set GET variable for account page to display password change message
        if (basename($_SERVER['PHP_SELF']) != "account.php") header("Location: /labour/src/Account Page/account.php?password_change=1");
    }

    $methods = ["GET", "POST"];
    if ($method != null && in_array($method, $methods)) {
        if ($_SERVER["REQUEST_METHOD"] != $method) terminate("Invalid request method", 400);
    }

    if ($variables != null) {
        if ($method == "GET") {
            foreach ($variables as $variable) {
                if (!isset($_GET[$variable])) {
                    terminate("Missing GET variables", 400);
                }
            }
        } else if ($method == "POST") {
            foreach ($variables as $variable) {
                if (!isset($_POST[$variable])) {
                    terminate("Missing POST variables: $variable", 400);
                }
            }
        }
    }
}

/**
 * Checks if the user is an admin. If not, terminates the script with an error message.
 */
function check_is_admin() {
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
        terminate("You do not have permission to access this page or perform this action", 403);
    }
}