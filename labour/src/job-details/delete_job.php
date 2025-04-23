<?php
if (isset($_SERVER["REQUEST_METHOD"])) { // added an extra check, that way I can include this file in tests without it running the security check (ignore the errors for terminate and securit check ;P)
    require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
    security_check_and_connect("POST", ["job_id"]);
    
    // --------------- MAIN --------------- //
        
    // Get POST data
    $job_id = $_POST['job_id'];
    
    // call the function
    $code = delete_job($pdo, $job_id);
    switch ($code) {
        case "success":
            terminate("Successfully Deleted Job", 200);
            break;
        case "empassigned":
            terminate("Job has employees assigned", 400);
            break;
        case "jobdoesntexist":
            terminate("Job does not exist", 400);
            break;
        }
}

/**
 * Deletes a job and all assignments with it (assignments get deleted through ON DELETE CASCADE on database).
 *  Can only delete if no employees are assigned to the job
 * @param PDO $pdo connection object
 * @param string $job_id id of job to be deleted
 * @return string  success/error code
 */
function delete_job($pdo, $job_id)
{
    //// error checking
    // check if job exists
    $stmt = $pdo->prepare("SELECT * FROM job WHERE id = ?");
    $stmt->execute([$job_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result === false) {
        return "jobdoesntexist";
    }
    // check if any employees assigned
    $stmt = $pdo->prepare("SELECT COUNT(*) AS numEmp FROM worksOn WHERE job_id = ?");
    $stmt->execute([$job_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result['numEmp'] != 0) {
        return "empassigned";
    }

    //// delete the job
    $stmt = $pdo->prepare("DELETE FROM job WHERE id = ?");
    $stmt->execute([$job_id]);

    // return success
    return "success";
}
?>