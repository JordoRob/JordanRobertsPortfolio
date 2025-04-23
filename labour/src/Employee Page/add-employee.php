<?php

// this isset() is here to prevent all of the following code from running in tests
if (isset($_SERVER['REQUEST_METHOD'])) {
    // --------------- SECURITY --------------- //
    require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
    security_check_and_connect("POST", array('name', 'phone', 'phoneSec', 'email', 'datehired', 'birthday', 'notes', 'title'));

    // Make sure file is set
    $file = null;
    if (isset($_FILES['upload'])) {
        $file = $_FILES['upload'];
    }

    //// create employee
    $code = add_employee($_POST['name'], $_POST['phone'], $_POST['phoneSec'], $_POST['email'], $_POST['datehired'], $_POST['birthday'], $_POST['notes'], $_POST['title'], $file, $pdo);
    if (!$code[0]) {
        terminate(json_encode($code), 400);
    } else {
        terminate(json_encode($code), 200);
    }
}


// --------------- MAIN --------------- //
function add_employee($name, $phone, $phoneSec, $email, $datehired, $birthday, $notes, $title, $file, $pdo)
{
    require_once dirname(__DIR__) . "/employee info overlay/fileUpload.php"; //We use these functions

    if (empty($datehired)) { //Since they are date fields, the db doesnt like getting an empty string
        $datehired = null;
    }
    if (empty($birthday)) {
        $birthday = null;
    }

    // error checking
    if (!strlen($name) > 0) { //Make sure required things are filled in
        return (array(false, "Please fill in name"));
    }
    if (!is_numeric($title)) { //Make sure role is valid
        return (array(false, "Invalid role value, please refresh the page and try again"));
    }
    if (strlen($email) > 200) { //make sure email isnt too long
        return (array(false, "Invalid email value"));
    }
    if ((strlen($phone) > 14 || (strlen($phone) < 14 && strlen($phone) > 0)) || (strlen($phoneSec) > 14 || (strlen($phoneSec) < 14 && strlen($phoneSec) > 0))) { //Phone must be 0 or 14 nothing else
        return (array(false, "Invalid phone value"));
    }
    if (strlen($name) > 100) {
        return (array(false, "Name too long"));
    }

    if ($file['size'] != 0) { //if they chose a file, make sure its correct.
        $validation = image_validate($file);
        if (!$validation[0]) {
            return ($validation);
        }
    }
    $stmt = $pdo->prepare("INSERT INTO employee (name, phoneNum, phoneNumSecondary, email, hired, birthday, notes, role) VALUES (?,?,?,?,?,?,?,?)");
    $result = $stmt->execute([$name, $phone, $phoneSec, $email, $datehired, $birthday, $notes, $title]);

    if (!$result) {
        return (array(false, "Error: Insert failed"));
    } else {
        $new_id = $pdo->lastInsertId(); //grab the new id
        if ($file['size'] != 0) { //if they uploaded a file, add it
            if ($validation[0]) {
                save_user_image($new_id, $file);
            }
        }
        return array(true, $new_id);
    }
}

?>