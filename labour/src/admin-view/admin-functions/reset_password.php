<?php
if (isset($_SERVER["REQUEST_METHOD"])) { 
    require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
    security_check_and_connect("POST", ["id"]);
    check_is_admin();
    $res = resetPassword($pdo, $_POST['id']);
    handleTerminationP($res);
}

function resetPassword($pdo, $id){
    // Check user exists and is not admin
    $stmt = $pdo->prepare("SELECT username FROM user WHERE id = ? AND is_admin = 0");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        //terminate("Selected manager does not exist", 400);
        return "Selected manager does not exist";
    }

    // random generate 5 numbers to append to username
    $temp_pass = $user['username'] . rand(10000, 99999);

    $stmt = $pdo->prepare("UPDATE user SET password = ?, recent_password_reset = true WHERE id = ?");
    $stmt->execute([md5($temp_pass), $id]);


    return [
        "code" => "Password reset successfully.",
        "data" => [
            "temp_pass" => $temp_pass
        ]
    ];
}

function handleTerminationP($result) {

    if (is_string($result)) {
        terminate ($result, 400);
    }
    elseif (is_array($result) && isset($result['code'])) {
        switch ($result['code']) {
            case 'Password reset successfully.':
                $responseData = $result['data'];
                terminate(json_encode([
                    "message" => "Password reset successfully.",
                    "temp_pass" => $responseData['temp_pass']
                ]), 200);
                break;
            default:
            terminate ("An error occurred", 500);
                break;
        }
    } else {
        terminate ("An error occurred", 500);
    }
}