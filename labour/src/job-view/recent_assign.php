<?php
// this file is referenced at the start of move_employee
//This function is for keeping track of employees assigned history

function end_recent_assignment($emp_id,$job_id,$pdo,$currentDate = null){ 
    date_default_timezone_set("America/Vancouver");
    if($currentDate==null){
        $currentDate= date("Y-m-d");    //99% of the time besides test cases where we need to mess with things
    }
    $stmt = $pdo->prepare("SELECT start_date FROM assignments WHERE job_id=? AND employee_id=? AND end_date IS NULL");
    $stmt->execute([$job_id, $emp_id]);
    $start=$stmt->fetchColumn();
    if($start==$currentDate." 00:00:00"||$start==$currentDate){ //if start date and end date are the same then the row should be deleted
        $stmt = $pdo->prepare("DELETE FROM assignments WHERE job_id=? AND employee_id=? AND end_date IS NULL");
        $stmt->execute([$job_id,$emp_id]);
    }else{  //if not, give em an end date
        $stmt = $pdo->prepare("UPDATE assignments SET end_date=? WHERE job_id=? AND employee_id=? AND end_date IS NULL");
        $stmt->execute([$currentDate, $job_id,$emp_id]);
    }

}

function add_recent_assignment($emp_id,$job_id,$assigner,$pdo,$date=null){
    date_default_timezone_set("America/Vancouver");
    if($date==null){
        $date=date('Y-m-d');
    }

    $stmt = $pdo->prepare("INSERT into assignments (start_date,job_id,employee_id,assigner) VALUES (?,?,?,?)"); //create an assignment :)
    $stmt->execute([$date,$job_id,$emp_id,$assigner]);
}
?>