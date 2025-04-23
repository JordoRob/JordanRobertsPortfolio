<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
security_check_and_connect();
error_reporting(E_ALL);

function generate_employee_overlay($search = "", $active_code) { //Employee overlay in job page
    global $pdo;
    $generated_html = "";
    try {
        $search = trim($search); //trim search if there is one
        $employeeQuery = "
            SELECT name, role, img, id
            FROM employee 
            WHERE active >= 0
                AND IF(:active_code = -1, active=0 AND id NOT IN (SELECT employee_id FROM worksOn), active = :active_code)
                AND IF(:search = '', true, name LIKE CONCAT('%', :search, '%')) 
            ORDER BY role ASC, name ASC
        ";
        $employeeStmt = $pdo->prepare($employeeQuery);
        $employeeStmt->bindParam(':search', $search);
        $employeeStmt->bindParam(':active_code', $active_code);
        $employeeStmt->execute();

        if ($employeeStmt->rowCount() > 0) { //determine the title card for each section
            // Include global data table (translates active code to title)
            include $_SERVER['DOCUMENT_ROOT'] . "/labour/src/global_data_table.php";

            $section_title = $active_code == -1 ? "Unassigned" : $active_status[$active_code];

            $generated_html .= "
                <div id='section_title_$active_code' class='overlay section_title' onclick='minimize($active_code);' title='Hide/Show $section_title Employees'>
                    <span class='arrow'>▼</span>
                    <div class='section_title_text'>
                        <h1>" . htmlspecialchars($section_title) . "</h1>
                    </div>
                    <div class='employee_count'>
                        <p>" . $employeeStmt->rowCount() . "</p>
                    </div>                    
                </div>
            ";

            // Start employee wrapper
            $generated_html .= "<div><div class='overlay-content' name='section$active_code'>";

            while ($employee = $employeeStmt->fetch(PDO::FETCH_ASSOC)) {
                // Translate role to string
                $employee["img"]="../".$employee['img'];
                $employee["display_role"] = htmlspecialchars($display_role[$employee["role"]]);
                $employee["class_role"] = $class_role[$employee["role"]];
                if (!file_exists($employee["img"])) {
                    $employee["img"] = "../img/emp/default.svg";
                }
                // Generate employee button
                $generated_html .= "
                    <div class='employee " . $employee["class_role"] . "' id='employee[" . $employee["id"] . "]' data-emp_id='" . $employee["id"] . "' draggable='true' ondragstart='drag_employee(event)'>
                        <div class='employee_details'>
                            <h1>" . htmlspecialchars($employee["name"]) . "</h1>
                            <p>" . $employee["display_role"] . "</p>
                        </div>
                        <span class='employee_img_wrapper' onclick='employeeDetails(" . $employee["id"] . ")'>
                        <img draggable='false' src=" . $employee["img"] . ">
                        </span>
                    </div>
                ";
            } //same format as other employee listings

            // Close the employee wrapper
            $generated_html .= "</div></div>";
        }
        return $generated_html;
    } catch (PDOException $e) {
        terminate($e->getMessage(), 500);
    }
}

$search = isset($_POST["search"]) ? trim($_POST["search"]) : "";
$tab = isset($_POST["tab"]) ? $_POST["tab"] : 0; //determine which tab being requested, unassigned or all
$return_html = "";

if ($tab == 0) {
    $return_html .= generate_employee_overlay($search, -1);
} else {
    for ($i = 0; $i < 3; $i++) { //call it three times for each section
        $return_html .= generate_employee_overlay($search, $i);
    }
}

terminate($return_html);
?>
