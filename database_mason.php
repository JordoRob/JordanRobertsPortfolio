<?php
$env = parse_ini_file('database_info.env');
$server_name = "mysql-server";

$user_name = $env["DB_USER"];
$password = $env["DB_PASS"];
    $database = "masonsroom";
    // Creating the connection by specifying the connection details
    $connection = new mysqli($server_name, $user_name, $password, $database);
?>