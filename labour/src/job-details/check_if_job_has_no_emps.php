<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
security_check_and_connect("POST", ["job_id"]);

$stmt = $pdo->prepare("SELECT employee_id FROM worksOn WHERE job_id = ?");
$stmt->execute([$_POST['job_id']]);
$emps_assigned["id"] = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (count($emps_assigned["id"]) == 0) {
    terminate("No emps assigned", 200);
} else {
    //returns an array of all the emps on a job, used in job-info.js to check when archiving a job
    $in = str_repeat('?,', count($emps_assigned["id"]) - 1) . '?';
    $stmt = $pdo->prepare("SELECT name FROM employee WHERE id IN ($in)");
    $stmt->execute($emps_assigned["id"]);
    $emps_assigned["name"] = $stmt->fetchAll(PDO::FETCH_COLUMN);

    terminate(json_encode($emps_assigned), 400);
}
?>