<?php

if (isset($_SERVER["REQUEST_METHOD"])) { 
    require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
    security_check_and_connect("POST", ["start_date", 'title', 'end_date', 'manager', 'address']);

    if (strlen($_POST['title']) < 255 && strlen($_POST['address']) < 255 && strlen($_POST['manager']) < 255) {

        //a hacky fix that creates a manager and gets the id (please get rid of this when we change manager id stuff to just a VARCHAR)

        //check if end date is greater than start date if they are both set
        if (strlen($_POST['end_date']) > 0 && strlen($_POST['start_date']) > 0 && $_POST['end_date'] < $_POST['start_date']) {
            terminate("Error: start date is after end date", 400);
        }

        //if date is just year-month, add on the day (since mysql doesn't do just year-month) (this option should be chose for edge and chrome, since they use the month input type)
        //if date is year-month-day, leave as is (this option chosen for safari and firefox, since they don't support the month input type and need to use date input type)
        $start_date = $_POST['start_date'];
        if (strlen($_POST['start_date']) == 7) {
            $start_date = $_POST['start_date'] . "-01";
        } else if (strlen($_POST['start_date']) == 0) { //if no start date is chosen
            $start_date = null;
        }
        $end_date = $_POST['end_date'];
        if (strlen($_POST['end_date']) == 7) {
            $end_date = $_POST['end_date'] . "-01";
        } else if (strlen($_POST['end_date']) == 0) { //if no end date is chosen
            $end_date = null;
        }

        if (strlen($_POST['title']) < 1) {
            terminate("Error: Job title cannot be blank", 400);
        }

        // call the function 
        terminate(addJob($_POST['title'], $_POST['address'], $_POST['archived'], $_POST['manager'], $start_date, $end_date, $pdo));
    } else {
        terminate("Error: title, address, or manager fields too long (max is 255 chars)", 400);
    }
}

function addJob($title, $address, $archived, $manager, $start_date, $end_date, $pdo)
{
try {

        //insert a new job
        $sql = "INSERT INTO job (title, address, archived, manager_name, start_date, end_date) VALUES (:title, :address, :archived, :manager_name, :start_date, :end_date)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':address', $address);
        $stmt->bindValue(':archived', $archived);
        $stmt->bindValue(':manager_name', $manager);
        $stmt->bindValue(':start_date', $start_date);
        $stmt->bindValue(':end_date', $end_date);
        $stmt->execute();

        $last_id = $pdo->lastInsertId();

        return $last_id;

    } catch (Exception $e) {
        echo $e->getMessage();
        return false;
    }
}