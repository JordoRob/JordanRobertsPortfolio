<?php

if (isset($_SERVER["REQUEST_METHOD"])) { 
    require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
    security_check_and_connect("POST", ["old_pass", "new_pass", "confirm_pass"]);
    $res = changePassword($pdo, $_POST['old_pass'], $_POST['new_pass'], $_POST['confirm_pass'], $_SESSION['user']);
    handleTerminationChangePassword($res);
}



function changePassword($pdo, $oldPass, $newPass, $confirmPass, $userId) {
    $result = 'success'; // Default success message

    if ($oldPass == $newPass) {
        $result = 'same_password';
    } elseif (!preg_match("/^(?=.*[0-9])(?=.*[!_@#$%^&*])[a-zA-Z0-9!_@#$%^&*]{8,30}$/", $newPass)) { // Check password length 8-30 and regex must contain at least one number and one special character
        $result = 'invalid_password';
    } elseif ($newPass != $confirmPass) {
        $result = 'password_mismatch';
    } else {
        $stmt = $pdo->prepare("SELECT username FROM user WHERE id = ? AND password = md5(?)");
        $stmt->execute([$userId, $oldPass]);

        if ($stmt->rowCount() == 0) {
            $result = 'incorrect_old_password';
        } else {
            $stmt = $pdo->prepare("SELECT is_admin FROM user WHERE id = ?");
            $stmt->execute([$userId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row['is_admin'] == 1) {
                check_is_admin();
            }

            if ($result === 'success') {
                $stmt = $pdo->prepare("UPDATE user SET password = md5(?), recent_password_reset = false WHERE id = ?");
                $stmt->execute([$newPass, $userId]);

                // Unset recent password reset session variable
                unset($_SESSION['recent_password_reset']);
            }
        }
    }

    return $result;
}

function handleTerminationChangePassword($result) {
    switch ($result) {
        case 'success':
            terminate("Password changed successfully", 200);
            break;
        case 'same_password':
            terminate("New password cannot be the same as old password", 400);
            break;
        case 'invalid_password':
            terminate("Password must be 8-30 characters long and contain at least one number and one special character (!_@#$%^&*)", 400);
            break;
        case 'password_mismatch':
            terminate("Passwords do not match", 400);
            break;
        case 'incorrect_old_password':
            terminate("Old password is incorrect", 400);
            break;
        default:
            terminate("Unknown error", 500);
            break;
    }
}
    
?>