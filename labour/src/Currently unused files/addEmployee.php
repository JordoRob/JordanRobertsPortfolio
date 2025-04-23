<?php 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['role'])  && isset($_POST['name']) && isset($_POST['active']) && isset($_POST['archived'])) {

    //connect to database
    include dirname(__DIR__) .  "/database-con.php";
    $result = addEmployee($_POST['role'], $_POST['name'], $_POST['active'], $_POST['archived'], $pdo);
}
else{
    echo "Error: Invalid form submission.";
}


function addEmployee($role, $name, $active, $archived, $pdo){
    try {
        
        //insert a new employee
        $sql = "INSERT INTO employee (role, name, active, archived) VALUES (:role, :name, :active, :archived)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':role', $role);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':active', $active);
        $stmt->bindValue(':archived', $archived);
        $result = $stmt->execute();

        return true;

    }
    catch (Exception $e) {
        echo $e->getMessage();
        return false;
    }
}





