<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
security_check_and_connect("POST", ["emp_id"]);

$stmt = $pdo->prepare("SELECT job_id FROM worksOn WHERE employee_id = ?");
$stmt->execute([$_POST['emp_id']]);
$jobs_assigned["id"] = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (count($jobs_assigned["id"]) == 0) {
    terminate("No jobs assigned", 200);
} else {
    //returns an array of all the jobs worked by an employee, used in drag-drop.js to display what jobs the employee is about to be unassigned from
    $in = str_repeat('?,', count($jobs_assigned["id"]) - 1) . '?';
    $stmt = $pdo->prepare("SELECT title FROM job WHERE id IN ($in)");
    $stmt->execute($jobs_assigned["id"]);
    $jobs_assigned["title"] = $stmt->fetchAll(PDO::FETCH_COLUMN);

    terminate(json_encode($jobs_assigned), 418);
}
?>