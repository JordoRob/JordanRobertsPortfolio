<?php
if (isset($_SERVER['REQUEST_METHOD'])) {
    require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
    security_check_and_connect("POST", ["job_id"]);

    try {
        include $_SERVER['DOCUMENT_ROOT'] . "/labour/database-con.php";
        echo (json_encode(get_job_graph_data($pdo, $_POST['job_id'])));
    } catch (PDOException $e) {
        terminate($e->getMessage(), 500);
    }
}

function get_job_graph_data($pdo, $job_id, $rangestart = null, $rangeend = null)
{
    //get start and end date from the database
    $stmt = $pdo->prepare("SELECT start_date, end_date FROM job WHERE id=?");
    $stmt->execute([$job_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $start_date = $row['start_date'];
    $end_date = $row['end_date'];

    // $today = date_create('now')->format('Y-m-d');
    $today = date_create('now')->format('Y-m-d');
    // if no range is given, set the range to be 2 months before the start date and 2 months after the end date
    if ($rangestart == null) {
        // if the job doesn't have a start date, set the start date to be 6 months before today
        // if today is less than 2 months before the start date, set the rangestart to be 2 months before today
        if ($start_date == null) {
            $temp_start_date = $today;
            $rangestart = "DATE_SUB(?, INTERVAL 6 MONTH)";
        } else if ($today < date_sub(date_create_from_format("Y-m-d", $start_date), date_interval_create_from_date_string("2 months"))->format('Y-m-d')) {
            $temp_start_date = $today;
            $rangestart = "DATE_SUB(?, INTERVAL 2 MONTH)";
        } else {
            $rangestart = "DATE_SUB(?, INTERVAL 2 MONTH)";
            $temp_start_date = $start_date;
        }
    }
    if ($rangeend == null) {
        // if the job doesn't have a end date, set the rangeend to be 6 months after today
        // if today is greater than 2 months after the end date, set the rangeend to be 2 months after today
        if ($end_date == null) {
            $temp_end_date = $today;
            $rangeend = "DATE_ADD(?, INTERVAL 6 MONTH)";
        } else if ($today > date_add(date_create_from_format("Y-m-d", $end_date), date_interval_create_from_date_string("2 months"))->format('Y-m-d')) {
            $temp_end_date = $today;
            $rangeend = "DATE_ADD(?, INTERVAL 2 MONTH)";
        } else {
            $rangeend = "DATE_ADD(?, INTERVAL 2 MONTH)";
            $temp_end_date = $end_date;
        }
    }

    // query that gets projected employee count and actual employee count for each day in the range
    $outlookQuery = "
                WITH RECURSIVE cte AS (
                    SELECT $rangestart AS idate
                    UNION ALL
                    SELECT DATE_ADD(idate, INTERVAL 1 day) AS idate FROM cte 
                    
                    WHERE DATE_ADD(idate, INTERVAL 1 day) <= $rangeend
                )
                
                SELECT job.title, 
                idate,
                IF(idate > CURDATE(), NULL, COUNT(employee_id)) AS actual_emp_count, 
                IFNULL(outlook.count, 0) AS predicted_emp_count
                
                FROM cte CROSS JOIN job 
                
                LEFT OUTER JOIN outlook ON (job.id = outlook.job_id AND EXTRACT(YEAR_MONTH FROM idate) = EXTRACT(YEAR_MONTH FROM outlook.date))
                
                LEFT OUTER JOIN assignments ON (assignments.job_id = job.id AND idate >= assignments.start_date AND IF(assignments.end_date IS NULL, TRUE, idate <= assignments.end_date))
                
                WHERE job.id=?
                GROUP BY id, idate, outlook.count
                ORDER BY id ASC, idate ASC
            ";
    $outlookStmt = $pdo->prepare($outlookQuery);
    $outlookStmt->execute([$temp_start_date, $temp_end_date, $job_id]);
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
    
    $response = array($labels, $outlookDataset, $actualDataset, $start_date, $end_date, $today);
    return ($response);
}

?>