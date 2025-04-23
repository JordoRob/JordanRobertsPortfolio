<?php 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    //connect to database
    include dirname(__DIR__) .  "/database-con.php";
    archiveEmployee($_POST['id'], $pdo);
}
else{
    echo "Error: Invalid form submission.";
}

function archiveEmployee($id, $pdo){
    try{
        //remove employee from all jobs
        $sql = "DELETE FROM worksOn WHERE employee_id = :employee_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':employee_id', $id);
        $stmt->execute();

        //get current date
        $dateArchived = date("Y-m-d");

        //set employee to archived and inactive
        $sql = "UPDATE employee SET archived = :archived , active = :active WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':archived', $dateArchived);
        $stmt->bindValue(':active', 0);
        $stmt->execute();

        $pdo = null;//close connection
        return true;
    }
    catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }
}