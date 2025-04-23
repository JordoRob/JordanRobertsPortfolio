<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
security_check_and_connect();

$stmt = $pdo->prepare("SELECT * FROM employee WHERE active = 1 ORDER BY role ASC, name ASC");
$stmt->execute();
$inactive = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM employee WHERE active = 2 ORDER BY role ASC, name ASC");
$stmt->execute();
$in_school = $stmt->fetchAll(PDO::FETCH_ASSOC);

include $_SERVER['DOCUMENT_ROOT'] . "/labour/src/global_data_table.php";

for($i = 0; $i < count($inactive); $i++) {
    $empId = $inactive[$i]["id"];
    $empName = $inactive[$i]["name"];
    $empImg = $inactive[$i]["img"];
    $empRole = $inactive[$i]["role"];
    $empActive = $inactive[$i]["active"];

    $d_role = $display_role[$empRole];
    $c_role = $class_role[$empRole];

    if (!file_exists($empImg)) {
        $empImg = "/labour/src/img/emp/default.svg";
    }

    $inactive_html .= "
        <div class='employee $c_role' id='employee[$empId]' data-emp_id='$empId' draggable='true' ondragstart='drag_employee(event)' onclick='employeeDetails($empId)'>
            <div class='employee_details'>
                <h1 data-text='$empName'>$empName</h1>
                <p>$d_role</p>
            </div>
            <span class='employee_img_wrapper'>
            <img draggable='false' src=$empImg>
            </span>
        </div>
    ";
}

for($i = 0; $i < count($in_school); $i++) {
    $empId = $in_school[$i]["id"];
    $empName = $in_school[$i]["name"];
    $empImg = $in_school[$i]["img"];
    $empRole = $in_school[$i]["role"];
    $empActive = $in_school[$i]["active"];

    $d_role = $display_role[$empRole];
    $c_role = $class_role[$empRole];

    if (!file_exists($empImg)) {
        $empImg = "/labour/src/img/emp/default.svg";
    }

    $in_school_html .= "
        <div class='employee $c_role' id='employee[$empId]' data-emp_id='$empId' draggable='true' ondragstart='drag_employee(event)' onclick='employeeDetails($empId)'>
            <div class='employee_details'>
                <h1 data-text='$empName'>$empName</h1>
                <p>$d_role</p>
            </div>
            <span class='employee_img_wrapper'>
            <img draggable='false' src=$empImg>
            </span>
        </div>
    ";
}

if (count($inactive) == 0) {
    $inactive_html = "<h1 class='no_results'>No inactive employees</h1>";
}

if (count($in_school) == 0) {
    $in_school_html = "<h1 class='no_results'>No employees in school</h1>";
}

terminate(json_encode([
    "inactive" => $inactive_html,
    "in_school" => $in_school_html
]));
?>