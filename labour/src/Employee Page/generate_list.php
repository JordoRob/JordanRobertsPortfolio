<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
security_check_and_connect("POST", ["sort", "order", "pinned_emp_ids"]);

/**
 * Generates the employee section
 * Takes in the active code-number and pdo connection
 */
function generate_employee_section($active_code, $pdo)
{
    // ORDER BY sort and order query - checks and defaults
    $sort_list = array("name", "role", "hired", "redseal");
    $sort = in_array($_POST["sort"], $sort_list) ? $_POST["sort"] : "name";
    $order = $_POST["order"] == "asc" ? "ASC" : "DESC";

    // If sort by role, add subsort of by name
    $order .= $sort == "role" ? ", name ASC" : "";
    // Pull from database and create employee list
    $section_employees = $pdo->prepare("
        SELECT * 
        FROM employee 
        WHERE active = :active_code 
            AND IF(:search = '', true, name LIKE CONCAT('%', :search, '%'))
        ORDER BY " . $sort . " " . $order . "
    ");
    $section_employees->bindParam(":active_code", $active_code);
    $_POST["search"] = isset($_POST["search"]) ? trim($_POST["search"]) : ""; // Trim whitespace and set empty string if not set
    $section_employees->bindParam(":search", $_POST["search"]);
    $section_employees->execute();

    $pinned_emp_ids = $_POST["pinned_emp_ids"];
    // Generate section title
    generate_section_title($active_code, $section_employees->rowCount());

    // If there are employees, generate section wrapper
    

    // Start employee wrapper
    echo "<div><div class='employee_wrapper' name='section$active_code' data-active_code='$active_code' data-job_id='-1' ondrop='drop_employee(event)' ondragover='allowDrop(event)'>";

    // Loop through employees and generate employee buttons
    while ($employee = $section_employees->fetch(PDO::FETCH_ASSOC)) {
        // Translate role to string
        include $_SERVER['DOCUMENT_ROOT'] . "/labour/src/global_data_table.php";
        $employee["display_role"] = $display_role[$employee["role"]];
        $employee["class_role"] = $class_role[$employee["role"]];
        $employee['img'] = "../" . $employee['img'];
        $empName = htmlspecialchars($employee["name"]);
        if (!file_exists($employee["img"])) {
            $employee["img"] = "../img/emp/default.svg";
        }
    
        $is_pinned = in_array($employee["id"], $pinned_emp_ids) ? " duplicated" : "";
    
        // Generate employee button with htmlspecialchars
        echo "
            <div class='employee " . $employee["class_role"] . $is_pinned . "' id='employee[" . $employee["id"] . "]' data-emp_id='" . $employee["id"] . "' draggable='true' ondragstart='drag_employee(event)' onclick='employeeDetails(" . $employee["id"] . ")'>
                <div class='employee_details'>
                    <h1>$empName</h1>
                    <p>" . $employee["display_role"] . "</p>
                </div>
                <span class='employee_img_wrapper'>
                    <img draggable='false' src='" . htmlspecialchars($employee["img"]) . "'>
                </span>
            </div>
        ";
    }
    // Close the employee wrapper
    echo "</div></div>";
}

/**
 * Generates the section title
 */
function generate_section_title($active_code, $emp_count)
{
    // Creates $active_status variable with translated status
    include $_SERVER['DOCUMENT_ROOT'] . "/labour/src/global_data_table.php";

    echo "
        <div id='section_title_$active_code' class='section_title' onclick='minimize($active_code);' title='Hide/Show $active_status[$active_code] Employees'>
            <span class='arrow'>▼</span>
            <div class='section_title_text'>
                <h1>" . $active_status[$active_code] . "</h1>
            </div>
            <div class='employee_count'>
                <p>" . $emp_count . "</p>
            </div>
        </div>
    ";
}

// Generate employee list
try {
    // Generate all sections
    $max_active = 3;
    for ($active_code = $_POST["show_archived"] == "true" ? -1 : 0; $active_code < $max_active; $active_code++) {
        generate_employee_section($active_code, $pdo);
    }

    $pdo = null; //close connection
} catch (PDOException $e) {
    die($e->getMessage());
}

//     ,_     _
//     |\\_,-~/
//     / _  _ |    ,--.
//    (  @  @ )   / ,-'
//     \  _T_/-._( (
//     /         `. \
//    |         _  \ |
//     \ \ ,  /      |
//      || |-_\__   /
//     ((_/`(____,-'
//    Zecr Samanadro was here
?>