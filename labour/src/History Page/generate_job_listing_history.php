<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
security_check_and_connect("POST", ["sort", "order", "showAllJobs", "start_date", "end_date", "page", "per_page"]);
error_reporting(E_ALL);
// file gets called from job-view.php and job-view.js
$search = isset($_POST["search"]) ? trim($_POST["search"]) : "";

$sort_list = array("title", "manager_name", "emp", "search_start_date");
$sort = in_array($_POST["sort"], $sort_list) ? $_POST["sort"] : "title";
$showAllJobs = $_POST["showAllJobs"] == "true" ? true : false;
$order = $_POST["order"] == "asc" ? "asc" : "desc";

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

// error checks for per page and page (need to be numeric and positive)
$per_page = (is_numeric($_POST["per_page"]) && ($_POST["per_page"] > 0 && $_POST["per_page"] <= 250)) ? $_POST["per_page"] : 25;
$page = (is_numeric($_POST["page"]) && ($_POST["page"] > 0 && $_POST["page"] <= 2000000000)) ? $_POST["page"] - 1 : 0; // -1 because the page number starts at 1, but the sql query starts at 0

// import get graph data function
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/History Page/get-job-graph-data-history.php";

try {
    // Order by possible values
    // i'm so sorry for disgustifying this query <3 adam
    // i'll try to explain it though:
    // first if statement checks if the start_date or end_date exists, and if it does, format it, else just leave it null
    // the second (inner) if checks if start year and end year are not this year, if true -> show years & months, if false -> show just months
    // start_date_actual and end_date_actual are used if you would like to use the years anyway (for comparisons for highlighting the end/start date, for example). Format is: January 2023 (works with strtotime())
    // i also have regular start_date in there for the search
    $jobQuery = "
        SELECT job.id, job.title, manager_name, 
            IF(job.start_date, IF(YEAR(job.start_date) != YEAR(CURDATE()), DATE_FORMAT(job.start_date,'%b \'%y'), DATE_FORMAT(job.start_date,'%b')), NULL) AS start_date,
            IF(job.end_date, IF(YEAR(job.end_date) != YEAR(CURDATE()), DATE_FORMAT(job.end_date,'%b \'%y'), DATE_FORMAT(job.end_date,'%b')), NULL) AS end_date,
            IF(job.start_date, DATE_FORMAT(job.start_date,'%M %Y'), NULL) AS start_date_actual,
            IF(job.end_date, DATE_FORMAT(job.end_date,'%M %Y'), NULL) AS end_date_actual,
            job.start_date AS search_start_date,
            COUNT(worksOn.employee_id) as emp,
            archived
        FROM 
            job 
            LEFT JOIN worksOn ON job.id=worksOn.job_id 
        WHERE IF(:showAllJobs, TRUE, job.archived IS NOT null)
        AND IF(:search = '', true, title LIKE CONCAT('%', :search, '%'))
        GROUP BY job.id 
        ORDER BY $sort $order
        LIMIT :per_page OFFSET :amount_to_skip
    ";
    $jobStmt = $pdo->prepare($jobQuery);
    $jobStmt->bindParam(":search", $search);
    $jobStmt->bindParam(":showAllJobs", $showAllJobs);
    $jobStmt->bindParam(":per_page", $per_page, PDO::PARAM_INT);
    $amount_to_skip = $page * $per_page;
    $jobStmt->bindParam(":amount_to_skip", $amount_to_skip, PDO::PARAM_INT);
    $jobStmt->execute();

    // Array to keep track of duplicates
    $duplicates = array();

    $generated_html = "";
    // Check if any jobs were found
    if ($jobStmt->rowCount() > 0) {
        // Variable to store generated html. Initialize with the job wrapper.

        $rowNum = 0; // used to keep track whether it's the first job or the last job in the list (first gets a top legend, last gets an x-axis legend)
        // Loop through each job
        while ($jobRow = $jobStmt->fetch(PDO::FETCH_ASSOC)) {
            // check if top or bottom job (don't ask why json_encode, it's just how it works)
            $isStartOrBottomJob = ""; // used to add a class to the job listing if it's the first or last job in the list
            if ($rowNum == 0) {
                $isTopJob = json_encode(true);
                $isStartOrBottomJob = "top-job";
            } else {
                $isTopJob = json_encode(false);
            }
            if ($rowNum == $jobStmt->rowCount() - 1) {
                $isBottomJob = json_encode(true);
                $isStartOrBottomJob = "bottom-job";
            } else {
                $isBottomJob = json_encode(false);
            }


            $jobId = $jobRow['id'];
            $jobTitle = $jobRow['title'];
            $jobManager = $jobRow['manager_name'];
            $jobStart = is_null($jobRow['start_date']) ? '?' : $jobRow['start_date'];
            $jobEnd = is_null($jobRow['end_date']) ? '?' : $jobRow['end_date'];
            $jobStartActual = is_null($jobRow['start_date_actual']) ? 0 : strtotime($jobRow['start_date_actual']); //use for comparisons (like for hightling the start/end dates)
            $jobEndActual = is_null($jobRow['end_date_actual']) ? 2147000000 : strtotime($jobRow['end_date_actual']);
            $currentEmp = $jobRow['emp'];
            $archivedClass = $jobRow['archived'] == null ? "" : "archived";

            // get job data for graph
            // TODO: ADD START AND END RANGe
            // TODO: maybe have it so the all the graph's data is sent alongside the html as a json rather than shipping it inside the html function call
            $graph_data = get_job_graph_data_history($pdo, $jobId, $start_date, $end_date);
            $outlookdata = json_encode($graph_data[1]);
            $actualdata = json_encode($graph_data[2]);
            $graphlabels = json_encode($graph_data[0]);
            $startdate = json_encode($graph_data[3]);
            $enddate = json_encode($graph_data[4]);
            $today = json_encode($graph_data[5]);

            // Print job details
            $generated_html .= "
                <div class='job_listing $isStartOrBottomJob' data-job_id='$jobId'>
                    <div class='job_info $archivedClass' onclick='jobDetails($jobId)'>
                        <h1>$jobTitle</h1>
                        <div class='job_details'>
                            <p>$jobManager</p>
                            <p>$jobStart - $jobEnd</p>
                        </div>
                    </div>
                    <div class='canvas_container'>
                        <canvas id='graph-$jobId'></canvas>
                    </div>
                    <script>
                    // istopjob: $isTopJob, isbottomjob: $isBottomJob
                    outlook_graph_history($outlookdata, $actualdata, $graphlabels, $startdate, $enddate, $today, $jobId, $isTopJob, $isBottomJob);
                    </script>
                </div>
                ";

            $rowNum++;
        }
    } else {
        $generated_html .= "
            <div style='display: flex; justify-content: center; align-items: center; height: 100%; width: 100%;'>No jobs found! Check your search and filter settings.</div>
        ";
    }

    terminate($generated_html);
} catch (PDOException $e) {
    terminate($e->getMessage(), 500);
}