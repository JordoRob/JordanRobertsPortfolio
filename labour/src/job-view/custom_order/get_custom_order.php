<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
security_check_and_connect();
$user = isset($_SESSION['user']) ? $_SESSION['user'] : 2; // Projector view

$stmt = $pdo->prepare("SELECT custom_order FROM user WHERE id = ?");
$stmt->execute([$user]);
$custom_order = $stmt->fetchColumn();

// If is NULL (uninitialized), initialize the order with all non-archived jobs and save to database
if($custom_order == NULL) {
    $stmt = $pdo->prepare("SELECT id FROM job WHERE archived IS NULL");
    $stmt->execute();
    $custom_order = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $pdo->prepare("UPDATE user SET custom_order = ? WHERE id = ?");
    $stmt->execute([json_encode($custom_order), $user]);

    $custom_order = json_encode($custom_order);
}


terminate($custom_order);
?>