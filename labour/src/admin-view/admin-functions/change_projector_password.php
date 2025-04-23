<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
security_check_and_connect("POST", ["new_pass"]);
check_is_admin();

// Check password length 8-30 and regex must contain at least one number and one special character
if (!preg_match("/^(?=.*[0-9])(?=.*[!_@#$%^&*])[a-zA-Z0-9!_@#$%^&*]{8,30}$/", $_POST['new_pass'])) {
    // Show user password requirements
    terminate("Password must be 8-30 characters long and contain at least one number and one special character (!_@#$%^&*)", 400);
}

// Update password
$stmt = $pdo->prepare("UPDATE user SET password = md5(?), recent_password_reset = false WHERE username = 'projector'");
$stmt->execute([$_POST['new_pass']]);

terminate("Password changed successfully");
?>