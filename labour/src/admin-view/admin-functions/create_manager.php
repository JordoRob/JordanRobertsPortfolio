<?php


if (isset($_SERVER["REQUEST_METHOD"])) {
    require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
    security_check_and_connect("POST", ["username"]);
    check_is_admin();

    // Trim username
    $_POST['username'] = trim($_POST['username']);


    $code = createManager($pdo, $_POST['username']);
    handleTermination($code);
}

function createManager($pdo, $username)
{
    // Check username length 5-15
    if (strlen($username) < 5 || strlen($username) > 15) {
        return "USERNAME_RANGE";
        //terminate("Username must be 5-15 characters long", 400);
    }
    // Check if username is taken
    $stmt = $pdo->prepare("SELECT username FROM user WHERE username = ?");
    $stmt->execute([$username]);

    if ($stmt->rowCount() > 0) {
        return "USERNAME_TAKEN";
    }

    // random generate 5 numbers to append to username
    $temp_pass = $username . rand(10000, 99999);

    $stmt = $pdo->prepare("INSERT INTO user (username, password, is_admin) VALUES (:username, :password, 0)");
    $stmt->execute([
        ":username" => $username,
        ":password" => md5($temp_pass)
    ]);

    $id = $pdo->lastInsertId();

    return [
        "code" => "USER_CREATED",
        "data" => [
            "temp_pass" => $temp_pass,
            "username" => $username,
            "id" => $id
        ]
    ];
}


//function to handle termination for bypassing phpunit termination
function handleTermination($result)
{
    if (is_string($result)) {
        switch ($result) {
            case "USERNAME_TAKEN":
                terminate ("Username is taken", 400); 
                break;
            case "USERNAME_RANGE":
                terminate ("Username must be 5-15 characters long", 400);
                break;
            default:
            terminate ("An error occurred", 500);
            break;
        }
    } elseif (is_array($result) && isset($result['code']) && isset($result['data'])) {
        switch ($result['code']) {
            case "USER_CREATED":
                $data = $result['data'];
                terminate(json_encode([
                    "message" => "User created successfully.",
                    "temp_pass" => $data['temp_pass'],
                    "username" => $data['username'],
                    "id" => $data['id']
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

?>