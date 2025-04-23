<?php

include_once dirname(__DIR__) ."/job-view/recent_assign.php";


if (isset($_SERVER["REQUEST_METHOD"])) { // added an extra check, that way I can include this file in tests without it running the security check (ignore the errors for terminate and securit check ;P)
    require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
    security_check_and_connect("POST", ["job_id"]);
    
    // --------------- MAIN --------------- //
        
    // Get POST data
    $job_id = $_POST['job_id'];
    
    // call the function
    $code = archive_job($pdo, $job_id);
    switch ($code) {
        case "success":
            terminate("Successfully Archived Job", 200);
            break;
        case "jobalreadyarchived":
            terminate("Job is already archived", 400);
            break;
        case "jobdoesntexist":
            terminate("Job does not exist", 400);
            break;
        }
}

/**
 * Archives a job. Sets the archived field to be now for given job
 * @param PDO $pdo connection object
 * @param string $job_id id of job to be archived
 * @return string  success/error code
 */
function archive_job($pdo, $job_id)
{
    //// error checking
    // check if job exists
    $stmt = $pdo->prepare("SELECT * FROM job WHERE id = ?");
    $stmt->execute([$job_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result === false) {
        return "jobdoesntexist";
    }
    // check if job already archived
    $stmt = $pdo->prepare("SELECT archived FROM job WHERE id = ?");
    $stmt->execute([$job_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result['archived'] != null) {
        return "jobalreadyarchived";
    }

    //// unassign all employees from the job (remove them from worksOn and set the employee's most recent assignment end_date to now (with matching job id))
    // get all employees assigned to the job
    $stmt = $pdo->prepare("SELECT employee_id FROM worksOn WHERE job_id = ?");
    $stmt->execute([$job_id]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // for each employee, remove them from worksOn and set their most recent assignment end_date to now
    foreach ($result as $row) {
        $employee_id = $row['employee_id'];
        // remove from worksOn
        $stmt = $pdo->prepare("DELETE FROM worksOn WHERE job_id = ? AND employee_id = ?");
        $stmt->execute([$job_id, $employee_id]);
        // set most recent assignment end_date to now
        end_recent_assignment($employee_id, $job_id, $pdo);
    }

    //// archive the job
    $stmt = $pdo->prepare("UPDATE job SET archived = NOW() WHERE id = ?");
    $stmt->execute([$job_id]);

    // return success
    return "success";
}
?>