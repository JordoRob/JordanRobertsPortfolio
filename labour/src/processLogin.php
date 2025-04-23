<?php declare(strict_types=1); ?>
<?php
//check to make sure the right method is used, as well as the username and password are set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['password'])) {
    require dirname(__DIR__) . "/database-con.php";
    $login = loginUser($_POST['username'], $_POST['password'], $pdo);
    if ($login == 0) { // result found, therefore username and password match
        //display main screen
        header("Location: /labour/src/job-view/job-view.php");
    } else if ($login == 1) { //admin login
        //display admin screen
        header("Location: /labour/src/admin-view/documentation.php");
    } else if ($login == 2) {
        header("Location: /labour/src/job-view/projector_view/projector-view.php");
    } else { //no matches, incorrect username/password
        //inform user of invalid username/password
        header("Location: login.php?status=1");
    }



} else if ($_SERVER["REQUEST_METHOD"] == "GET") {
    echo "<p> internal error: I don't GET it, sorry. not </p>";
} else if (!isset($_POST['username']) || !isset($_POST['password'])) {
    echo "<p> internal error: username or password not set </p>";
} else { //should never run
    echo "<p> internal error: other error, god help us </p>";
}

function loginUser($username, $userpass, $pdo)
{
    try {
        $sql = "SELECT id, is_admin, recent_password_reset, use_background, refresh_timer FROM user WHERE username = :username AND password = md5(:password)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':password', $userpass);
        $result = $stmt->execute();
        if ($row = $stmt->fetch()) { //if row exists, user exists.
            
            session_start();
            
            // Does not set if logged in using projector account
            if ($row['is_admin'] <= 1) {
                $_SESSION['user'] = $row['id'];
                $_SESSION['is_admin'] = $row['is_admin']; 
                $_SESSION['use_background'] = $row['use_background'];
                $_SESSION['recent_password_reset'] = $row['recent_password_reset'];
                $_SESSION['timer']=$row['refresh_timer'];
            } else {
                $_SESSION['projector'] = $row['id'];
            }

            $pdo = null; //close connection
            return $row['is_admin'];
        } else { //no matches, incorrect username/password
            //inform user of invalid username/password
            $pdo = null; //close connection
            return -1;
        }
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}
?>