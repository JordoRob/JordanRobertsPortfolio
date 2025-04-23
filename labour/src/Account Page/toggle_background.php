<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
security_check_and_connect("POST", ['toggle']);

$stmt = $pdo->prepare("UPDATE user SET use_background = :use_background WHERE id = :id");
$stmt->bindValue(':use_background', $_POST['toggle']);
$stmt->bindValue(':id', $_SESSION['user']);

if ($stmt->execute()) {
    $_SESSION['use_background'] = $_POST['toggle'];
    terminate($_POST['toggle']);
} else {
    terminate("Error updating background preference.", 500);
}
?>