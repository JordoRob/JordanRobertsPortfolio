<?php
if (isset($_SERVER["REQUEST_METHOD"])) {
    //connect to the database
    require $_SERVER['DOCUMENT_ROOT'] . "/labour/database-con.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
    security_check_and_connect("POST", ["job_id", "manager", "startdate", "enddate", "address", "title", "notes"]);
    $code = editJobDetail($_POST['job_id'], $_POST['title'], $_POST['manager'], $_POST['address'], $_POST['startdate'], $_POST['enddate'],$_POST['notes'], $pdo);
    if ($code[0] == true) {
        terminate(json_encode($code), 200);
    } else if ($code[0] == false) {
        terminate(json_encode($code), 400); // techically there can be server errors (500) too
    }
}

function editJobDetail($id, $title, $manager, $address, $startDate, $endDate, $notes, $pdo)
{
    try {
        // hacky fix, pls change once manager is varchar instead of seperate table

        //check if end date is greater than start date if they are both set
        if (strlen($endDate) > 0 && strlen($startDate) > 0 && $endDate < $startDate) {
            return ([false, "Error: Start date is after end date"]);
        }

        //if date is just year-month, add on the day (since mysql doesn't do just year-month) (this option should be chose for edge and chrome, since they use the month input type)
        //if date is year-month-day, leave as is (this option chosen for safari and firefox, since they don't support the month input type and need to use date input type)
        if (strlen($startDate) == 7) {
            $startDate = $startDate . "-01";
        } else if (strlen($startDate) == 0) { //if no start date is chosen
            $startDate = null;
        }
        if (strlen($endDate) == 7) {
            $endDate = $endDate . "-01";
        } else if (strlen($endDate) == 0) { //if no end date is chosen
            $endDate = null;
        }

        if (strlen($title) < 1) {
            return [false, "Error: Job title cannot be blank"];
        }
        //update the job details
        $sql = "UPDATE job SET title = :title,address = :address, manager_name = :manager_name, start_date = :start_date, end_date = :end_date, notes= :notes WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':address', $address);
        $stmt->bindValue(':manager_name', $manager);
        $stmt->bindValue(':start_date', $startDate);
        $stmt->bindValue(':end_date', $endDate);
        $stmt->bindValue(':notes', $notes);
        $stmt->execute();

        return [true, "we did it!"];
    } catch (PDOException $e) {
        return [false, "Server Error: " . $e->getMessage()];

    }
}
?>