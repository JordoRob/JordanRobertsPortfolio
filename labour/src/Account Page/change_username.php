<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
security_check_and_connect("POST", ["new_username"]);

// Check if account is admin
if ($_SESSION['is_admin'] == 1) {
    terminate("The admin account cannot be changed", 400);
}

// Trim username
$_POST['new_username'] = strip_tags(trim($_POST['new_username']));

// Check if username is less than 5 characters or more than 30 characters
if (strlen($_POST['new_username']) < 5 || strlen($_POST['new_username']) > 30) {
    terminate("Username must be 5-30 characters long", 400);
}

// Check if new username is the same as old username
$stmt = $pdo->prepare("SELECT username FROM user WHERE id = ?");
$stmt->execute([$_SESSION['user']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row['username'] == $_POST['new_username']) {
    terminate("New username cannot be the same as old username", 400);
}

// Check if new username is already taken
$stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE username = ?");
$stmt->execute([$_POST['new_username']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row['COUNT(*)'] > 0) {
    terminate("Username already taken", 400);
}

// Update username
$stmt = $pdo->prepare("UPDATE user SET username = ? WHERE id = ?");
$stmt->execute([$_POST['new_username'], $_SESSION['user']]);

// Make JSON response
$response = [
    "new_username" => $_POST['new_username'],
    "message" => "Username changed successfully"
];
terminate(json_encode($response), 200);
?>
