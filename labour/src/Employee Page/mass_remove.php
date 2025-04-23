<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
security_check_and_connect("POST", ["emp_id"]);

require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/job-view/recent_assign.php";

try {
    // Connect to database and begin transaction
    $pdo->beginTransaction();

    // Give employee an end date for all assignments
    $stmt = $pdo->prepare("SELECT job_id FROM worksOn WHERE employee_id = ?");
    $stmt->execute([$_POST['emp_id']]);
    $jobs = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($jobs as $job) {
        end_recent_assignment($_POST['emp_id'], $job, $pdo);
    }

    // Remove employee from all assignments
    $stmt = $pdo->prepare("DELETE FROM worksOn WHERE employee_id = ?");
    $stmt->execute([$_POST['emp_id']]);

    // Commit changes
    $pdo->commit();

    terminate("", 200);
} catch (PDOException $e) {
    // If there is an error, rollback changes
    $pdo->rollBack();
    terminate("Error when removing employee from assigned jobs", 500);
}
?>