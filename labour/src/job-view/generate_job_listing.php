<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
security_check_and_connect("POST", ["sort", "order"]);
error_reporting(E_ALL);
// file gets called from job-view.php and job-view.js
$search = isset($_POST["search"]) ? trim($_POST["search"]) : "";

$sort_list = array("title", "manager_name", "emp", "search_start_date");
$sort = in_array($_POST["sort"], $sort_list) ? $_POST["sort"] : "title";

$order = $_POST["order"] == "asc" ? "asc" : "desc";

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
            COUNT(worksOn.employee_id) as emp 
        FROM 
            job 
            LEFT JOIN worksOn ON job.id=worksOn.job_id 
            LEFT JOIN employee ON worksOn.employee_id=employee.id
        WHERE job.archived IS null
        AND IF(:search = '', true, title LIKE CONCAT('%', :search, '%') OR employee.name LIKE CONCAT('%', :search, '%'))
        GROUP BY job.id 
        ORDER BY $sort $order;
    ";
    $jobStmt = $pdo->prepare($jobQuery);
    $jobStmt->bindParam(":search", $search);
    $jobStmt->execute();

    // Array to keep track of duplicates
    $duplicates = array();

    $generated_html = "";
    // Check if any jobs were found
    if ($jobStmt->rowCount() > 0) {
        // Variable to store generated html. Initialize with the job wrapper.

        // Loop through each job
        while ($jobRow = $jobStmt->fetch(PDO::FETCH_ASSOC)) {
            $jobId = $jobRow['id'];
            $jobTitle = htmlspecialchars($jobRow['title']);
            $jobManager = htmlspecialchars($jobRow['manager_name']);
            $jobStart = is_null($jobRow['start_date']) ? '?' : htmlspecialchars($jobRow['start_date']);
            $jobEnd = is_null($jobRow['end_date']) ? '?' : htmlspecialchars($jobRow['end_date']);
            $jobStartActual = is_null($jobRow['start_date_actual']) ? 0 : strtotime($jobRow['start_date_actual']);  //use for comparisons (like for hightling the start/end dates)
            $jobEndActual = is_null($jobRow['end_date_actual']) ? 2147000000 : strtotime($jobRow['end_date_actual']);
            $currentEmp = $jobRow['emp'];
        
            // Query to get employees for the job
            $employeeQuery = "
                SELECT e.name, e.role, e.img, e.id
                FROM 
                    employee e INNER JOIN worksOn w 
                    ON e.id = w.employee_id
                WHERE w.job_id = :jobId AND e.active >= 0
                ORDER BY e.role ASC, e.name ASC
            ";
            $employeeStmt = $pdo->prepare($employeeQuery);
            $employeeStmt->bindParam(':jobId', $jobId);
            $employeeStmt->execute();
        
            // Print job details
            $generated_html .= "
                <div class='job_listing' data-job_id='$jobId'>
                    <div class='job_info' onclick='jobDetails($jobId)'>
                        <h1>$jobTitle</h1>
                        <div class='job_details'>
                            <p>$jobManager</p>
                            <p>$jobStart - $jobEnd</p>
                        </div>
                    </div>
                ";

            // Check if any employees were found
            $generated_html .= "<div class='employee_wrapper' data-job_id='$jobId' ondrop='drop_employee(event)' ondragover='allowDrop(event)'>";
            $numDuplicates = 0;
            if ($employeeStmt->rowCount() > 0) {
                //for showing accurate current info
                while ($employeeRow = $employeeStmt->fetch(PDO::FETCH_ASSOC)) {
                    //Employee data from query
                    $empName = htmlspecialchars($employeeRow["name"]);
                    $empImg = "../" . htmlspecialchars($employeeRow["img"]);
                    $empId = $employeeRow["id"];

                    if (!file_exists($empImg)) {
                        $empImg = "/labour/src/img/emp/default.svg";
                    }
                    // Translate role to string
                    include $_SERVER['DOCUMENT_ROOT'] . "/labour/src/global_data_table.php";
                    $d_role = $display_role[$employeeRow["role"]];
                    $c_role = $class_role[$employeeRow["role"]];


                    // Check for duplicates
                    $is_duplicate = "";
                    $marker = "";
                    if (in_array($empId, $duplicates)) { // If person in array, they are a duplicate
                        $is_duplicate = "duplicated";
                        $marker = "[duplicated]";
                        $numDuplicates++; // keep track of how many duplicates
                    } else {
                        $duplicates[] = $empId; // if not, add them to the array
                    }

                    $generated_html .= "
                        <div class='employee $c_role $is_duplicate' id='employee[$empId]$marker' data-emp_id='$empId' draggable='true' ondragstart='drag_employee(event)' onclick='employeeDetails($empId)'>
                            <div class='employee_details'>
                                <h1>$empName</h1>
                                <p>$d_role</p>
                            </div>
                            <span class='employee_img_wrapper'>
                            <img draggable='false' src=$empImg>
                            </span>
                        </div>
                    ";
                }
            }

            // Close the employee wrapper
            $generated_html .= "</div>";

            // Print current and forecast
            $generated_html .= "
                <div class='job_forecast' id='jobForecast[$jobId]'>
                    <div class='current'>
                        <h1>Current</h1>";
                        // employee sum container (for the job)
                        if ($numDuplicates > 0) {
                            $realEmps = $currentEmp - $numDuplicates;
                            $generated_html .= "<div> $currentEmp <div title='Without Duplicates' style='Font-size:50%'>($realEmps)</div></div>"; //show the duplicates removed
                        } else {
                            $generated_html .= "<div>$currentEmp</div>";
                        }
                        $generated_html .= "
                        </div>
                    <div class='forecast'>
            ";

            // Query to get outlook for the job
            $outlookQuery = "
                SELECT DATE_FORMAT(date,'%M %Y') AS dateFormatted, count
                FROM outlook
                WHERE 
                    job_id = :jobId 
                    AND date > CURDATE() 
                    AND date <= DATE_ADD(CURDATE(), INTERVAL 12 MONTH) 
                ORDER BY date
            ";
            $outlookStmt = $pdo->prepare($outlookQuery);
            $outlookStmt->bindParam(':jobId', $jobId);
            $outlookStmt->execute();

            // Check if any outlook information was found
            $currDate = date('Y-m-d H:i:s', strtotime("now")); //get the current date to compare other ones against
            $currDate = new DateTime($currDate);

            // refactored this whole section since the old one would write all the outlooks from the database first, and then write the rest of the entries
            //// Loop through each month, if there's an outlook entry for the outlook, generate it, otherwise write a 0 (aka a fake entry)
            $outlookRow = $outlookStmt->fetch(PDO::FETCH_ASSOC); //get first row
            for ($available_forecast = 0; $available_forecast < 12; $available_forecast++) {

                // increment the currDate incrementer
                date_add($currDate, date_interval_create_from_date_string("1 month"));

                // The month shown above each entry
                $visualDate = $currDate->format("M");

                // real outlook date is retrieved from database, fake is the incrementor
                $realoutlookDate = $outlookRow ? strtotime($outlookRow['dateFormatted']) : NULL;
                $fakeoutlookDate = strtotime(($currDate->format("F Y")));

                // if the database outlook date is after the iterator (or we're out of outlook rows from the database), then write a 0 and go to next iteration
                if ($outlookRow == NULL || $realoutlookDate > $fakeoutlookDate) {
                    $outlookNum = 0;
                } else { // else write the actual outlook entry and fetch the next row (to be used in next iteration)
                    // retrieve the projection number
                    $outlookNum = $outlookRow['count'];
                    $outlookRow = $outlookStmt->fetch(PDO::FETCH_ASSOC);
                }

                // for highlighting start date/end date/dates after end date
                $outlookDateActual = strtotime(($currDate->format("F Y")));
                $isStartOrEndClass = ''; //for highlight the start date, end date, and days after end date
                if ($outlookDateActual < $jobStartActual) {
                    $isStartOrEndClass = 'job-before-start';
                } else if ($outlookDateActual == $jobStartActual) {
                    $isStartOrEndClass = 'job-start';
                } else if ($outlookDateActual == $jobEndActual) {
                    $isStartOrEndClass = 'job-end';
                } else if ($outlookDateActual > $jobEndActual) {
                    $isStartOrEndClass = $outlookNum > 0 ? 'job-overtime' : 'job-after-end';
                }

                $deficitClass = "meeting";
                $deficitTitle = "Current # of employees is equal to what is required for " . $currDate->format("F Y");
                if ($outlookNum > $currentEmp) {
                    $deficitClass = "deficit";
                    $deficitTitle = "Current # of employees is less than is required for " . $currDate->format("F Y");
                } else if ($outlookNum < $currentEmp) {
                    $deficitClass = "surplus";
                    $deficitTitle = "Current # of employees is greater than is required for " . $currDate->format("F Y");
                }
                if ($outlookNum == 0 || $currentEmp == 0) {
                    $deficitClass = "";
                    $deficitTitle = "";
                }
                // create the forecastVal id
                $forecastValId = "forecastVal[$jobId][" . date_format($currDate, "m-Y") . "]' jobid='$jobId' date='" . date_format($currDate, "Y-m");

                ////actually writing the entry
                $generated_html .= "
                <div class='month-wrap $isStartOrEndClass' id='forecastEntry[$jobId][" . date_format($currDate, "m-Y") . "]' title='$deficitTitle'>
                    <label for='$forecastValId'>$visualDate</label>
                    <input type='number' class='forecast-value $deficitClass' id='$forecastValId' onClick='this.select();' min='0' max='99' value='$outlookNum'>
                </div>";

            }

            // Close the forecast container
            $generated_html .= "</div></div></div>";
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