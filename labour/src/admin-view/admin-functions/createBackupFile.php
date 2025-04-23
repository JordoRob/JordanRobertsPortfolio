<?php

if (isset($_SERVER["REQUEST_METHOD"])) { 
    require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
    check_is_admin();
    createBackupFile();
}


function createBackupFile(){
    $backupFileName  = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    $tempBackupFilePath = 'tempBackups/' . $backupFileName;

    $mysqldumpCommand = "mysqldump -hmysql-server -uroot -psecret HE_Schedule > {$tempBackupFilePath} 2>&1";

    // Execute the mysqldump command
    exec($mysqldumpCommand, $output, $returnVar);

    if ($returnVar === 0) {
        // Set appropriate headers for file download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $backupFileName . '"');
        header('Content-Length: ' . filesize($tempBackupFilePath));

        // Send the file to the browser for download
        readfile($tempBackupFilePath);

        // Delete the temporary backup file after download
        unlink($tempBackupFilePath);
        
        // Terminate the script
        exit;
    } else {
        echo '<script>alert("Failed to load the backup file. Error output: ' . implode("\n", $output) . '");</script>';
    }
}

?>



