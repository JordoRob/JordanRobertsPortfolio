<?php

if (isset($_SERVER['REQUEST_METHOD'])) {
    require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
    security_check_and_connect("POST", ["job_id"]);

    try {
        include $_SERVER['DOCUMENT_ROOT'] . "/labour/database-con.php";
        echo (json_encode(job_overlay_generate($pdo, $_POST['job_id'])));
    } catch (PDOException $e) {
        terminate($e->getMessage(), 500);
    }
}
function job_overlay_generate($pdo, $job_id)
{
    $infoQuery = "
    SELECT 
        j.id, title, address, archived, manager_name as manager, start_date, end_date, COUNT(w.employee_id) as numEmp, outlookNum, notes 
    FROM 
        job j 
        LEFT JOIN worksOn w ON j.id=w.job_id
        LEFT JOIN (SELECT count AS outlookNum, job_id FROM outlook WHERE job_id=? AND date=?) as sq1
            ON j.id=sq1.job_id
    WHERE j.id = ?
    ";
    $currMonth = date_create('now')->format('Y-m');
    $currMonth .= "-01";
    $infoStmt = $pdo->prepare($infoQuery);
    $infoStmt->execute([$job_id, $currMonth, $job_id]);

    // Check if employee was found
    if ($infoStmt->rowCount() > 0) {
        require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/global_data_table.php";

        $row = $infoStmt->fetch();
        $generated_html = "
        <img src='/labour/src/img/close.svg' class='close_overlay' onclick='closeOverlay(`job-overlay`);'>
        <div class='overlay_assignments_wrapper overlay_employee_wrapper'>
            <span class='assignment_title'>Assigned Employees</span>
            <div class='job-overlay-employee-container'>
    ";
        if ($row['archived'] == null) {
            $empQuery = "
        SELECT e.name, e.role, e.img, e.id
        FROM 
            employee e INNER JOIN worksOn w 
            ON e.id = w.employee_id
        WHERE w.job_id = :job_id AND e.active >= 0
        ORDER BY e.role ASC, e.name ASC
        ";
        } else {
            $empQuery = "
            SELECT e.name, e.role, e.img, e.id
            FROM 
                employee e INNER JOIN assignments w 
                ON e.id = w.employee_id
            WHERE w.job_id = :job_id AND e.active >= 0
            ORDER BY e.role ASC, e.name ASC
            ";
        }
        $employeeStmt = $pdo->prepare($empQuery);
        $employeeStmt->bindParam(':job_id', $job_id);
        $employeeStmt->execute(); //getting the employees
        if ($employeeStmt->rowCount() > 0) {
            //for showing accurate current info
            while ($employeeRow = $employeeStmt->fetch(PDO::FETCH_ASSOC)) {
                //Employee data from query
                $empName = $employeeRow["name"];
                $empImg = "../" . $employeeRow["img"];
                $empId = $employeeRow["id"];

                if (!file_exists($empImg)) {
                    $empImg = "../img/emp/default.svg";
                }
                // Translate role to string (seperated class role from displayed role since classes can't have spaces)
                $d_role = $display_role[$employeeRow["role"]];
                $c_role = $class_role[$employeeRow["role"]];

                $generated_html .= "
                    <div class='employee $c_role no-drag' id='employee[$empId]' data-emp_id='$empId' onclick='employeeDetails($empId)'>
                        <div class='employee_details'>
                            <h1>" . htmlspecialchars($empName) . "</h1>
                            <p>" . htmlspecialchars($d_role) . "</p>
                        </div>
                        <span class='employee_img_wrapper'>
                        <img draggable='false' src=$empImg>
                        </span>
                    </div>
                ";
            }
        }
        if ($row['archived'] != null) {
            $active = "Archived";
        } else {
            if ($row['end_date'] == null || date($row['end_date']) > date_create('now')->format('Y-m-d H:i:s')) {
                $active = "On-Time";
            } else {
                $active = "Over-Time";
            }
            if ($row['outlookNum'] > $row['numEmp']) {
                $assigned = "Under Projection";
                $assignIdentify = "Under";
            } else {
                if ($row['outlookNum'] == $row['numEmp']) {
                    $assigned = "On Projection";
                    $assignIdentify = "On";
                } else {
                    $assigned = "Above Projection";
                    $assignIdentify = "Above";
                }
            }
            $assigned .= " " . $row['numEmp'] . "/" . $row['outlookNum'];
        }

        $end_date = $row['end_date'];
        $start_date = $row['start_date'];
        $start_month = substr($start_date, 0, 7); // just take the month for the start and end month and end month fields YYYY-MM
        $end_month = substr($end_date, 0, 7);
        $generated_html .= "</div>";
        if ($row['archived'] == null) 
        $generated_html .= "<div class='overlay_emp_count'><p>Total</p><div class='overlay_card card-assigned' title='Assigned Employees'>" . $row['numEmp'] . "</div></div>";
        $generated_html .= "</div>";
        $generated_html .= "
            <div class='info-wrapper' data-job-id='$job_id'>
                <div class='overlay_head'>
                    <div class='overlay_head_text'>
                        <span class='info-name' id='job-title-datafield'>" . htmlspecialchars($row['title']) . "</span>
                        <span class='info-line'></span>
                        <span id='job-manager-datafield' class='info-subheading'>" . htmlspecialchars($row['manager']) . "</span>
                        <span id='job-manager-error' class='job-error invalid-feedback'></span>
                        <span id='job-title-error' class='job-error invalid-feedback'></span>
                    </div>
                </div>
                <div class='overlay_info_cards'>
                <div class='overlay_card card-$active'>" . htmlspecialchars($active) . "</div>";
        
        // don't show is over/is under projection card if archived
        if ($row['archived'] == null) {
            $generated_html .= "<div class='overlay_card card-$assignIdentify'>" . htmlspecialchars($assigned) . "</div>";
        }

        $generated_html .= "<img class='overlay-button' title='Delete' src='/labour/src/img/delete.svg' id='overlay_delete' onclick='deleteJob(" . $job_id . ", \"" . rawurlencode($row['title']) . "\")'>";
        
        // show archive button if not archived, otherwise show unarchive button (uses same id fyi)
        if ($row['archived'] == null) {
            $generated_html .= "<img class='overlay-button' title='Archive' src='/labour/src/img/Interface/archive_hollow.svg' id='overlay_archive' onclick='archiveJob(" . $job_id . ", \"" . rawurlencode($row['title']) . "\")'>";
        } else {
            $generated_html .= "<img class='overlay-button' title='Unarchive' src='/labour/src/img/Interface/unarchive.svg' id='overlay_archive' onclick='unarchiveJob(" . $job_id . ", \"" . rawurlencode($row['title']) . "\")'>";
        }

        $generated_html .= "
                <img class='overlay-button overlay_edit' title='Edit Job Details' src='/labour/src/img/edit.svg' id='overlay_edit_job_$job_id' onclick='enableEditJob()'>
                </div>
                <div class='overlay_info_box_wrapper'>
                    <div class='overlay_job_info_wrapper'>
                    <div class='overlay_double_wrapper'>
                    <div class='overlay_info_box overlay_box_half'>
                        <span>Start Date</span>
                        <span id='job-startdate-datafield'>" . htmlspecialchars($start_month) . "</span>
                        <span id='job-startdate-error' class='job-error invalid-feedback'></span>
                    </div>
                    <div class='overlay_info_box overlay_box_half'>
                        <span>End Date</span>
                        <span id='job-enddate-datafield'>" . htmlspecialchars($end_month) . "</span>
                        <span id='job-enddate-error' class='job-error invalid-feedback'></span>
                        </div>
                    </div>
                    <div class='overlay_info_box'>
                        <span>Address</span>
                        <span id='job-address-datafield'>" . htmlspecialchars($row['address']) . "</span>
                        <span id='job-address-error' class='job-error invalid-feedback'></span>
                    </div>
                    </div>
                    <div class='job_overlay_notes'>
                    <span>Notes:</span>
                    <textarea disabled id='job-notes-datafield'>" . htmlspecialchars($row['notes']) . "</textarea>
                </div>
                </div>
                <canvas id='graph'></canvas>
                </div>
            ";
        return ($generated_html);
    }
}
?>