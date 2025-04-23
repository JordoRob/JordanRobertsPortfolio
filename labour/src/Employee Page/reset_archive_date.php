<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
security_check_and_connect("POST", ["emp_id"]);

try {
    $stmt = $pdo->prepare("UPDATE employee SET archived = NULL WHERE id = ?");
    $stmt->execute([$_POST['emp_id']]);
    terminate("Successfully reset archive date", 200);
} catch (PDOException $e) {
    terminate($e->getMessage(), 500);
}
?>