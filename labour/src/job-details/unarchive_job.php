<?php

if (isset($_SERVER["REQUEST_METHOD"])) { // added an extra check, that way I can include this file in tests without it running the security check (ignore the errors for terminate and securit check ;P)
    require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
    security_check_and_connect("POST", ["job_id"]);
    
    // --------------- MAIN --------------- //
        
    // Get POST data
    $job_id = $_POST['job_id'];
    
    // call the function
    $code = unarchive_job($pdo, $job_id);
    switch ($code) {
        case "success":
            terminate("Successfully Unarchived Job", 200);
            break;
        case "jobalreadyunarchived":
            terminate("Job is already unarchived", 400);
            break;
        case "jobdoesntexist":
            terminate("Job does not exist", 400);
            break;
        }
}

/**
 * unArchives a job. Sets the archived field back to null for given job
 * @param PDO $pdo connection object
 * @param string $job_id id of job to be unarchived
 * @return string  success/error code
 */
function unarchive_job($pdo, $job_id)
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
    if ($result['archived'] == null) {
        return "jobalreadyunarchived";
    }

    //// archive the job
    $stmt = $pdo->prepare("UPDATE job SET archived = null WHERE id = ?");
    $stmt->execute([$job_id]);

    // return success
    return "success";
}
?>