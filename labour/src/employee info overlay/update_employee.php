<?php
session_start();

// --------------- SECURITY --------------- //
if (isset($_SERVER["REQUEST_METHOD"])) {
    require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
    security_check_and_connect("POST", ["emp_id", "name", "phone", "phoneSec", "email", "datehired", "birthday", "notes", "title","active"]);

    // Get POST data
    $emp_id = $_POST['emp_id'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $phoneSec = $_POST['phoneSec'];
    $email = $_POST['email'];
    $datehired = $_POST['datehired'];
    $birthday = $_POST['birthday'];
    $notes = $_POST['notes'];
    $title = $_POST['title'];
    $active = $_POST['active'];

    // May not exist
    $datearchived = $_POST['datearchived'];

    if (empty($datehired)) { //Since they are date fields, the db doesnt like getting an empty string
        $datehired = null;
    }
    if (empty($datearchived)) {
        $datearchived = null;
    }
    if (empty($birthday)) {
        $birthday = null;
    }

    // error checking
    if (strlen($name) > 50 || strlen($name) < 1) { //make sure name isnt too long
        terminate("Name length: 1 - 50 characters please", 400);
    }
    if (strlen($email) > 200) { //make sure email isnt too long
        terminate("Email too long", 400);
    }
    if ((strlen($phone) > 14 || (strlen($phone) < 14 && strlen($phone) > 0)) || (strlen($phoneSec) > 14 || (strlen($phoneSec) < 14 && strlen($phoneSec) > 0))) { //Phone must be 0 or 14 nothing else
        terminate("Invalid phone value", 400);
    }
    if (!is_numeric($title)) {
        terminate("Invalid role value - please try refreshing the page and try again", 400);
    }
    if (!is_numeric($emp_id)) {
        terminate("Invalid employee id - please try refreshing the page and try again", 400);
    }

    updateEmployee($pdo, $emp_id, $name, $phone, $phoneSec, $email, $datehired, $datearchived, $birthday, $notes, $title, $active);

}
function updateEmployee(PDO $pdo, $emp_id, $name, $phone, $phoneSec, $email, $datehired, $datearchived, $birthday, $notes, $title, $active) {
    try {
        $stmt = $pdo->prepare("UPDATE employee SET name = ?, phoneNum = ?, phoneNumSecondary = ?, email = ?, hired = ?, archived = ?, birthday = ?, notes = ?, role = ?, active=? WHERE id = ?");
        $result = $stmt->execute([$name, $phone, $phoneSec, $email, $datehired, $datearchived, $birthday, $notes, $title, $active, $emp_id]);

        if (!$result) {
            http_response_code(400);
            die("Error: Employee does not exist");
        }

        echo "Success";
    } catch (PDOException $e) {
        http_response_code(500);
        die("Error: " . $e->getMessage());
    }
}
?>