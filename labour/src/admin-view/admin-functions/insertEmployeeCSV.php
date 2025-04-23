<?php


if (isset($_POST['data'])) {

    include_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/global_generators.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
    security_check_and_connect("POST", ["data"]);
    check_is_admin();
    $data = $_POST['data'];

    echo generate_nav_bar(2, 2);
    insertEmployees($data, $pdo);
    $pdo = null;
 }



function insertEmployees($data, $pdo)
{
    foreach ($data as $row) {
        $name = $row['name'];
        $role = $row['role'];
        $active = $row['active'];
        $birthday = $row['birthday'] !== '' ? $row['birthday'] : null;
        $phoneNum1 = $row['phoneNum1'];
        $phoneNum2 = $row['phoneNum2'];
        $email = $row['email'];
        $hireDate = $row['hireDate'] !== '' ? $row['hireDate'] : null;
        // $redSeal = $row['redseal'];

        $notes = '';
        // if phone number 1 length is greater than 14, set notes to be the phone number and set phone number to be null
        if (strlen($phoneNum1) > 14) {
            $notes .= "Phone number 1: $phoneNum1\n";
            $phoneNum1 = null;
        }
        // and same for phone number 2
        if (strlen($phoneNum2) > 14) {
            $notes .= "Phone number 2: $phoneNum2\n";
            $phoneNum2 = null;
        }

        if (empty($name)) {
            continue;
        }

        $checkStmt = $pdo->prepare("SELECT id FROM employee WHERE name = :name AND birthday = :birthday");
        $checkStmt->bindParam(':name', $name);
        $checkStmt->bindParam(':birthday', $birthday);

        $checkStmt->execute();
        $existingEmployee = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if (!$existingEmployee) {
            // Insert an employee if not already in the table
            $insertStmt = $pdo->prepare("INSERT INTO employee (name, role, active, birthday, phoneNum, phoneNumSecondary, email, hired, notes)
            VALUES (:name, :role, :active, :birthday, :phoneNum, :phoneNumSecondary, :email, :hireDate, :notes)");
            $insertStmt->bindParam(':name', $name);
            $insertStmt->bindParam(':role', $role);
            $insertStmt->bindParam(':active', $active);
            $insertStmt->bindParam(':birthday', $birthday);
            $insertStmt->bindParam(':phoneNum', $phoneNum1);
            $insertStmt->bindParam(':phoneNumSecondary', $phoneNum2);
            $insertStmt->bindParam(':email', $email);
            $insertStmt->bindParam(':hireDate', $hireDate);
            $insertStmt->bindParam(':notes', $notes);
            //$insertStmt->bindParam(':redseal', $redSeal);

            try {
                $insertStmt->execute();
                echo "Employee inserted successfully. Employee Name: " . $name . ", Role: " . getRoleName($role);
                echo "<br>";
            } catch (PDOException $e) {
                echo "Error inserting employee: " . $e->getMessage();
                echo "<br>";
            }
        } else {
            echo "Employee already exists and not inserted. Employee Name: " . $name . ", Role: " . getRoleName($role);
            echo "<br>";
        }
    }
}

function getRoleName($role)
{
    switch ($role) {
        case 0:
            return 'Foreman';
        case 1:
            return 'Superintendent';
        case 2:
            return 'Journeyman';
        case 3:
            return '4th Year';
        case 4:
            return '3rd Year';
        case 5:
            return '2nd Year';
        case 6:
            return '1st Year';
        default:
            return 'Unknown';
    }
}

?>

<html>

<head>
    <link rel="stylesheet" href="/labour/src/global_styles/styles.css">
    <link rel="stylesheet" href="/labour/src/global_styles/navbar.css">
</head>

<body>
    <?php

    ?>
</body>

</html>