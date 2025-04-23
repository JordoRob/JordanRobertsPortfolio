<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
security_check_and_connect("POST", ["emp_id", "drop_active_code"]);

// --------------- MAIN --------------- //

// Get POST data
$emp_id = $_POST['emp_id'];
$drop_active_code = $_POST['drop_active_code'];

// Get employee current active code from database. And name
$stmt = $pdo->prepare("SELECT active, name FROM employee WHERE id = ?");
$stmt->execute([$emp_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if employee exists
if (!$row) {
    die("Error: Employee does not exist");
}

$emp_active_code = $row['active'];
$emp_name = $row['name'];

// If employee is already in the section, return error
if ($emp_active_code == $drop_active_code) {
    die("Error: Employee is already in the section");
}

// If employee is not in the section, move employee to section
$stmt = $pdo->prepare("UPDATE employee SET active = ? WHERE id = ?");
$stmt->execute([$drop_active_code, $emp_id]);

// Active code to section name
include $_SERVER['DOCUMENT_ROOT'] . "/labour/src/global_data_table.php"; // Contains translation array $active_status
echo "$emp_name | " . $active_status[$emp_active_code] . " → " . $active_status[$drop_active_code];
$pdo=null;
?>