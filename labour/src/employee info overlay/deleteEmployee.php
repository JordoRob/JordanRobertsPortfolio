<?php 
if (isset($_SERVER["REQUEST_METHOD"])) { // added an extra check, that way I can include this file in tests without it running the security check (ignore the errors for terminate and securit check ;P)
    require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
    security_check_and_connect("POST", ["id"]);
    
    if (deleteEmployee($_POST['id'], $pdo)) {
        terminate("Successfully deleted employee", 200);
    } else {
        terminate("Error deleting employee", 500);
    }
}

function deleteEmployee($id, $pdo) {
    try {
        // Delete the employee from the employee table
        $sql = "DELETE FROM employee WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        return true;
    } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }
}