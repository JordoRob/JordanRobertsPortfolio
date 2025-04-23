<?php
if (isset($_SERVER['REQUEST_METHOD'])) {
    require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
    security_check_and_connect("POST", ["last_check", "page"]);
    terminate(json_encode(update_check($_POST['last_check'], $_POST['page'], $pdo)), 200);
}
function update_check($last_check, $page, $pdo)
{ //last_check is in seconds from epoch <3
    date_default_timezone_set("America/Vancouver");
    //$last_check=$last_check+25200;  //The database refuses to have the right timezone so i have to do this I guess. It adds 7 hours FIXED IT
    $last_check = date("Y-m-d H:i:s", $last_check);

    // retrieve different checks for different pages
    if ($page == "job") // job checks if there are updates on WorksOn, employee, job, and outlook
        $stmt = $pdo->prepare("SELECT last_update FROM update_time WHERE last_update>=? ORDER BY last_update DESC");
    else if ($page == "employee") // employee just checks if any updates on employee table
        $stmt = $pdo->prepare("SELECT last_update FROM update_time WHERE table_name like 'employee' AND last_update>=?");
    $stmt->execute([$last_check]);

    if ($stmt->rowCount() > 0) {
        return (array(true, "$page has recent updates!", "last check: $last_check"));
    } else {
        return (array(false, "$page has no recent updates.", "last check: $last_check"));
    }
}

?>