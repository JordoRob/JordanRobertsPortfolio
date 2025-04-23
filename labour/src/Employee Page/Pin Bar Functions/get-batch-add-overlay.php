<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
security_check_and_connect();

// --------------- MAIN --------------- //

$job_details = $pdo->query("SELECT id, title FROM job WHERE archived IS NULL ORDER BY title ASC");

$job_names = $job_details->fetchAll(PDO::FETCH_ASSOC);

terminate(json_encode($job_names), 200);
?>