<?php
// if (isset($_SERVER['REQUEST_METHOD'])) {
//     require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
//     security_check_and_connect("POST", ["job_id"]);

//     try {
//         include $_SERVER['DOCUMENT_ROOT'] . "/database-con.php";
//         echo (json_encode(get_job_graph_data_history($pdo, $_POST['job_id'])));
//     } catch (PDOException $e) {
//         terminate($e->getMessage(), 500);
//     }
// }

/**
 * Returns the data needed to display the job graphs on the history page
 * @param PDO $pdo
 * @param int $job_id
 * @param string $rangestart needs to be in the format of "YYYY-MM-DD"
 * @param string $rangeend needs to be in the format of "YYYY-MM-DD"
 */
function get_job_graph_data_history($pdo, $job_id, $rangestart = null, $rangeend = null)
{
    // check to make sure rangestart and rangeend are right format
    if ($rangestart != null && !preg_match("/\d{4}-\d{2}-\d{2}/", $rangestart)) {
        terminate("Invalid date format for rangestart", 400);
    }
    if ($rangeend != null && !preg_match("/\d{4}-\d{2}-\d{2}/", $rangeend)) {
        terminate("Invalid date format for rangeend", 400);
    }



    // if no rangestart was given, set it to 1.5 years before today
    if ($rangestart == null) {
        $rangestart = date_create('now')->modify('-18 month')->format('Y-m-d');
    }
    // if no rangeend was given, set it to 1.5 years after today
    if ($rangeend == null) {
        $rangeend = date_create('now')->modify('+18 month')->format('Y-m-d');
    }
    




    //get start and end date from the database
    $stmt = $pdo->prepare("SELECT start_date, end_date FROM job WHERE id=?");
    $stmt->execute([$job_id]);
    if ($stmt->rowCount() == 0) {
        terminate("Job not found", 404);
    }
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $start_date = $row['start_date'];
    $end_date = $row['end_date'];

    // if start date or end date is before startrange or after endrange, set it to null so it doesn't get shown on graph
    if ($start_date != null && $start_date < $rangestart || $start_date > $rangeend) {
        $start_date = null;
    }
    if ($end_date != null &&  $end_date < $rangestart || $end_date > $rangeend) {
        $end_date = null;
    }


    // set the max recursion depth to be 2000 for this session (needed for recursive cte to go more than 1 year)
    $pdo->exec("SET SESSION max_recursive_iterations = 2000");

    // query that gets projected employee count and actual employee count for each day in the range
    $outlookQuery = "
                WITH RECURSIVE cte AS (
                    SELECT :rangestart AS idate
                    UNION ALL
                    SELECT DATE_ADD(idate, INTERVAL 1 day) AS idate FROM cte 
                    
                    WHERE DATE_ADD(idate, INTERVAL 1 day) <= :rangeend
                )
                
                SELECT job.title, 
                idate,
                IF(idate > CURDATE(), NULL, COUNT(employee_id)) AS actual_emp_count, 
                IFNULL(outlook.count, 0) AS predicted_emp_count
                
                FROM cte CROSS JOIN job 
                
                LEFT OUTER JOIN outlook ON (job.id = outlook.job_id AND EXTRACT(YEAR_MONTH FROM idate) = EXTRACT(YEAR_MONTH FROM outlook.date))
                
                LEFT OUTER JOIN assignments ON (assignments.job_id = job.id AND idate >= assignments.start_date AND IF(assignments.end_date IS NULL, TRUE, idate <= assignments.end_date))
                
                WHERE job.id= :job_id
                GROUP BY id, idate, outlook.count
                ORDER BY id ASC, idate ASC
            ";
    $outlookStmt = $pdo->prepare($outlookQuery);
    $outlookStmt->bindParam(":job_id", $job_id);
    $outlookStmt->bindParam(":rangestart", $rangestart);
    $outlookStmt->bindParam(":rangeend", $rangeend);
    $outlookStmt->execute();
    // Check if any outlook information was found
    $labels = array();
    $outlookDataset = array();
    $actualDataset = array();

    // loop through the dataset and put them into arrays (sucks that there isn't a pdo function that does this)
    while ($outlookRow = $outlookStmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($labels, $outlookRow['idate']);
        array_push($outlookDataset, $outlookRow['predicted_emp_count']);
        array_push($actualDataset, $outlookRow['actual_emp_count']);
    }

    // get today's date
    $today = date_create('now')->format('Y-m-d');



    $response = array($labels, $outlookDataset, $actualDataset, $start_date, $end_date, $today);
    return ($response);
}

?>