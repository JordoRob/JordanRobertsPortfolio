<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
security_check_and_connect("POST", ["custom_order", "type"]);

$user_id = $_SESSION['user'];
if ($_POST["type"] == 1) {
    $user_id = 2; // Projector view
}

$stmt = $pdo->prepare("UPDATE user SET custom_order = ? WHERE id = ?");
$stmt->execute([$_POST["custom_order"], $user_id]);
terminate("Successfully updated custom order", 200);
?>