<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
security_check_and_connect("POST", ["emp_id"]);

$emp_id = $_POST['emp_id'];

// Show all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $infoQuery = "
        SELECT 
            role, 
            name, 
            active, 
            archived, 
            img, 
            birthday, 
            phoneNum, 
            phoneNumSecondary, 
            notes, 
            email, 
            hired, 
            COUNT(w.job_id) as assigned
        FROM 
            employee 
            LEFT JOIN worksOn w ON employee.id=w.employee_id
        WHERE id = :emp_id
    ";
    $infoStmt = $pdo->prepare($infoQuery);
    $infoStmt->bindParam(":emp_id", $emp_id);
    $infoStmt->execute();

    // Check if employee was found
    if ($infoStmt->rowCount() > 0) {
        require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/global_data_table.php";

        $row = $infoStmt->fetch();
        $row["img"] = "../" . $row["img"];
        if (!file_exists($row["img"])) {
            $row["img"] = "/labour/src/img/emp/default.svg";
        }
        if ($row['assigned'] > 0) //if they have any entries in workson, set the 'assigned info card'
            $current = 'Assigned';
        else
            $current = 'Unassigned';
        $generated_html = "
            <img src='/labour/src/img/close.svg' class='close_overlay' onclick='closeOverlay()'>
            <div class='overlay_assignments_wrapper'>
                <span class='assignment_title'>Recent Assignments</span>
        ";
        $assignQuery = "
            SELECT title, a.start_date, a.end_date, u.username
            FROM
                assignments a
                JOIN job j ON a.job_id=j.id
                JOIN user u ON a.assigner=u.id
            WHERE a.employee_id=:emp_id
            ORDER BY a.start_date DESC
        ";
        $assignStmt = $pdo->prepare($assignQuery);
        $assignStmt->bindParam(':emp_id', $emp_id);
        $assignStmt->execute(); //getting the recent assignments
        if ($assignStmt->rowCount() > 0) {
            while ($assignRow = $assignStmt->fetch(PDO::FETCH_ASSOC)) {
                if ($assignRow['end_date'] == null) {
                    $assignRow['end_date'] = "Current";
                }
                $generated_html .= "
                <div class='overlay_assignment'>
                    <span class='assignment_job_title'>" . $assignRow['title'] . "</span>
                    <span class='assignment-dates'>" . $assignRow['start_date'] . ">" . $assignRow['end_date'] . "</span>
                    <span class='assignment-assigner'>Assigned by: " . $assignRow['username'] . "</span>
                </div>
            ";
            }
        } else {
            $generated_html .= "
            <div class='overlay_assignment'>
                <span class='assignment-title'>None!</span>
            </div>";
        }
        $code=trim($active_status[$row['active']]," ");
        $generated_html .= "</div>";
        $generated_html .= "
            <div class='info-wrapper' data-emp_id='$emp_id' data-active=".$row['active'].">
                <div class='overlay_head'>
                    <div class='overlay_head_text'>
                        <span class='info-name' id='name_datafield'>" . htmlspecialchars($row['name']) . "</span>
                        <span class='info-line'></span>
                        <span class='info-subheading' id='info-title' data-title='" . $row['role'] . "'>" . htmlspecialchars($display_role[$row['role']]) . "</span>
                    </div>
                    <div class='overlay_profile_pic_wrapper'>
                        <img class='overlay_profile_pic' src='" . $row['img'] . "' id='edit-employee-profile-pic'>
                        <form id='image-upload' enctype='multipart/form-data'>
                            <input name='upload' type='file' id='profile_upload' accept='image/*' onchange='uploadImage(this)' hidden/>
                            <label for='profile_upload' title='Upload Picture' id='file_upload_label'>
                                <span>Click to Upload New Image</span>
                            </label>
                        </form>
                    </div>
                </div>
                <div class='overlay_info_cards'>
                    <div class='overlay_card card-" . str_replace(' ','-',$code). "' id='status-datafield' data-statuscode='".$row['active']."' data-statusName='" . $code . "'>" . $active_status[$row['active']] . "</div>
                    <div class='overlay_card card-$current'>" . $current . "</div>
                    <img class='overlay-button' title='Delete' src='/labour/src/img/delete.svg' id='overlay_delete' onclick='deleteEmployee(" . $emp_id . ", \"" . rawurlencode(rawurlencode($row['name'])) . "\");;'>
                    <img class='overlay-button overlay_edit' title='Edit' src='/labour/src/img/edit.svg' id='overlay_edit_emp_$emp_id' onclick='enableEdit()'>
                </div>
                <div class='overlay_info_box_wrapper'>
                    <div class='overlay_double_wrapper'>
                        <div class='overlay_info_box overlay_phone'>
                            <span>Personal Number</span>
                            <span id='phone_datafield'>" . $row['phoneNum'] . "</span>
                        </div>
                        <div class='overlay_info_box overlay_phone'>
                            <span>Work Number</span>
                            <span id='phone_datafield_secondary'>" . $row['phoneNumSecondary'] . "</span>
                        </div>
                    </div>
                    <div class='overlay_info_box'>
                        <span>Email</span>
                        <span id='email_datafield'>" . htmlspecialchars($row['email']) . "</span>
                    </div>
        ";

        if (!$row['archived'] == null) {
            $generated_html .= "
                    <div class='overlay_double_wrapper'>
                        <div class='overlay_info_box'>
                            <span>Date Hired</span>
                            <span id='datehired_datafield'>" . $row['hired'] . "</span>
                        </div>
                        <div class='overlay_info_box'>
                            <span>Date Archived</span>
                            <span id='datearchived_datafield'>" . $row['archived'] . "</span>
                        </div>
                    </div>
            ";
        } else {
            $generated_html .= "
                    <div class='overlay_info_box'>
                        <span>Date Hired</span>
                        <span id='datehired_datafield'>" . $row['hired'] . "</span>
                    </div>
            ";
        }

        $generated_html .= "                    
                    <div class='overlay_info_box'>
                        <span>Birthday</span>
                        <span id='birthday_datafield'>" . $row['birthday'] . "</span>
                    </div>
                </div>
                <div class='overlay_notes'>
                    <span>Notes:</span>
                    <textarea disabled id='notes_datafield'>" . htmlspecialchars($row['notes']) . "</textarea>
                </div>
            </div>
        ";
        terminate($generated_html);
    }
} catch (PDOException $e) {
    die($e->getMessage());
}

?>