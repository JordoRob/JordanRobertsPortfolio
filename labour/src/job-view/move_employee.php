<?php

include_once dirname(__DIR__) ."/job-view/recent_assign.php";

if(isset($_SERVER["REQUEST_METHOD"])){
    require_once dirname(__DIR__) . "/session_security.php";
    security_check_and_connect("POST", ["emp_id", "start_job_id", "end_job_id"]);

    require "../../database-con.php";
    

    //----------------------- MAIN ------------------------------

    // Get POST data
    $emp_id = $_POST['emp_id'];
    $start_job_id = $_POST['start_job_id'];
    $end_job_id = $_POST['end_job_id'];

    // If start job and end job are the same, return error
    if ($start_job_id == $end_job_id) {
        terminate("Error: Start job and end job are the same", 400);
    }

    // If employee is already working on end job, return error
    $stmt = $pdo->prepare("SELECT * FROM worksOn WHERE employee_id = ? AND job_id = ?");
    $stmt->execute([$emp_id, $end_job_id]);
    if ($stmt->rowCount() > 0) {
        terminate("Error: " . get_name($pdo, $emp_id) . " is already working on " . get_title($pdo, $end_job_id), 400);
    }

    // If start_job is -1, add employee to end job and return success
    if ($start_job_id == -1) {
        $code = add_assignment($pdo, $emp_id, $end_job_id);
        if($code == "tooreal"){
            terminate("Error: " . get_name($pdo, $emp_id) . " is already working on " . get_title($pdo, $job_id), 400);
        }
        else{
        $title = get_title($pdo, $end_job_id);
        terminate(get_name($pdo, $emp_id) . " | ⌧ → " . $title);
        }
    }

    // If end_job is -1, remove employee from start job and return success
    else if ($end_job_id == -1) {
        $code = delete_assignment($pdo, $emp_id, $start_job_id);
        if($code=="notreal"){
            terminate("Error: " . get_name($pdo, $emp_id) . " is not working on " . get_title($pdo, $job_id), 400);
        }else{
        $title = get_title($pdo, $start_job_id);
        terminate(get_name($pdo, $emp_id) . " | " . $title . " → ⌧");}
    }

    // If all validation passes, move employee from start job to end job
    else {
        $code = move_assignment($pdo, $emp_id, $start_job_id, $end_job_id);
        if($code=="notreal"){
            terminate("Error: " . get_name($pdo, $emp_id) . " is not working on " . get_title($pdo, $start_job_id), 400);}
        else{
        $start_title = get_title($pdo, $start_job_id);
        $end_title = get_title($pdo, $end_job_id);
        terminate(get_name($pdo, $emp_id) . " | " . $start_title . " → " . $end_title);}
    }
}
function delete_assignment($pdo, $emp_id, $job_id, $date=null) {
    $stmt = $pdo->prepare("DELETE FROM worksOn WHERE employee_id = ? AND job_id = ?");
    $stmt->execute([$emp_id, $job_id]);
    if ($stmt->rowCount() == 0) {
        return "notreal";
    }else{
        end_recent_assignment($emp_id,$job_id,$pdo,$date);
        return "success";
    }
}

function add_assignment($pdo, $emp_id, $job_id,$date=null) {
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO worksOn (employee_id, job_id) VALUES (?, ?)");
        $stmt->execute([$emp_id, $job_id]);
        add_recent_assignment($emp_id,$job_id,$_SESSION['user'],$pdo,$date);
        $stmt = $pdo->prepare("UPDATE employee SET active = ? WHERE id = ?");
        $stmt->execute([0, $emp_id]);
        $pdo->commit();
        return "success";
    } catch (PDOException $e) {
        $pdo->rollBack();
        if ($e->getCode() == 23000) { // Duplicate entry error
            return "tooreal";
        } else { // Unknown error
            throw $e;
        }
    }
}

function move_assignment($pdo, $emp_id, $start_job_id, $end_job_id,$date=null) {
    $stmt = $pdo->prepare("UPDATE worksOn SET job_id = ? WHERE employee_id = ? AND job_id = ?");
    $stmt->execute([$end_job_id, $emp_id, $start_job_id]);
    if ($stmt->rowCount() == 0) {
        return "notreal";
    }else{
        end_recent_assignment($emp_id,$start_job_id,$pdo,$date);
        add_recent_assignment($emp_id,$end_job_id,$_SESSION['user'],$pdo);
        return "success";
    }
}

function get_title($pdo, $job_id) {
    $stmt = $pdo->prepare("SELECT title FROM job WHERE id = ?");
    $stmt->execute([$job_id]);
    return $stmt->fetchColumn();
}

function get_name($pdo, $emp_id) {
    $stmt = $pdo->prepare("SELECT name FROM employee WHERE id = ?");
    $stmt->execute([$emp_id]);
    return $stmt->fetchColumn();
}

