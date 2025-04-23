<?php
if (isset($_SERVER["REQUEST_METHOD"])) { // added an extra check, that way I can include this file in tests without it running the security check (ignore the errors for terminate and securit check ;P)
    require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
    security_check_and_connect("POST", ["jobid", "date", "count"]);
    
    // --------------- MAIN --------------- //
        
    // Get POST data
    $job_id = $_POST['jobid'];
    $date = $_POST['date'] . '-01';
    $count = $_POST['count'];
    
    $code = editProjectionNumber($pdo, $job_id, $date, $count);
    switch ($code) {
        case "success":
            terminate("Successfully updated ". date('F', strtotime($date)) . "'s projection number to " . $count);
            break;
        case "NaN":
            terminate("Must enter a number", 400);
            break;
        case "toobig":
            terminate("Number too big (must be less than 2147483647)", 400);
            break;
        case "baddateformat":
            terminate("Date Format Incorrect (must be YYYY-MM-01)", 400);
            break;
        }
}

/**
 * Updates a job's old projection number or gives it a new one, depending on if there is already one
 * existing for a given (job_id, date) combination
 * @param PDO $pdo connection object
 * @param string $job_id id of job to be updated
 * @param string $date the date of the job to be updated (preferably in format YYYY-MM-01)
 * @param string $count the new projection number 
 * @return string  success/error code -> 0 for success, 1 for count being NaN, 2 for count being too big
 */
function editProjectionNumber($pdo, $job_id, $date, $count)
{
    //// error checking
    //make sure count is numeric
    if (!is_numeric($count)) {
        return "NaN";
    }
    // make sure number isn't too big
    if ($count > 2147483647) {
        return "toobig";
    }
    // make sure date format is in YYYY-MM-01 format
    if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-01$/",$date)) {
        return "baddateformat";
    }

    //// edit projection number
    // first update the existing outlook if it's already set
    $sql = "UPDATE outlook SET count = :count WHERE job_id = :job_id AND date = :date";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':job_id', $job_id);
    $stmt->bindValue(':count', $count);
    $stmt->bindValue(':date', $date);
    $stmt->execute();
    // check if no rows have been changed; If so, it could either be a duplicate (e.g. setting count from 1 -> 1) or more simply an outlook hasn't been created yet (usually the latter)
    if ($stmt->rowCount() == 0) {
        $sql = "INSERT IGNORE INTO outlook( job_id, date, count) VALUES(:job_id, :date, :count)"; // use INSERT IGNORE to not insert duplicates and don't give warning
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':job_id', $job_id);
        $stmt->bindValue(':count', $count);
        $stmt->bindValue(':date', $date);
        $stmt->execute();
    }

    //close connection and return success
    return "success";
}
?>