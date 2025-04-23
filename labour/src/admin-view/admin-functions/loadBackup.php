<?php

if (isset($_SERVER["REQUEST_METHOD"])) { // added an extra check, that way I can include this file in tests without it running the security check (ignore the errors for terminate and securit check ;P)
    require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
    security_check_and_connect("POST", ["file"]);
    $pdo = null; // don't need you (told ya edwin that merging security_check and with a database connection was a bad idea haha)
    check_is_admin();
    loadBackup($_POST['file']);
}




function loadBackup($file){
    $backupDir = dirname(__DIR__) . '/../../backups/';
    $backupFile = $backupDir . $file;

    // Build the command to execute
    $command = "mysql -hmysql-server -uroot -psecret HE_Schedule < {$backupFile} 2>&1";

    // Execute the command
    $output = [];
    $returnVar = 0;
    exec($command, $output, $returnVar);
    
    if ($returnVar === 0) {
        echo '<script>alert("Backup file loaded successfully."); window.location.href = "../../../labour/src/job-view/job-view.php";</script>';
    } else {
        echo '<script>alert("Failed to load the backup file. Error output: ' . implode("\n", $output) . '"); window.location.href = "../../../labour/src/job-view/job-view.php";</script>';
    }
}
?>

