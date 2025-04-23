<?php
//credentials
$env = parse_ini_file('..\database_info.env');
$connString = "mysql:host=mysql-server;dbname=Schedule";
$user = $env["DB_USER"];
$password = $env["DB_PASS"];
$pdo;
try {
    //make connection
    $pdo = new PDO($connString, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    die($e->getMessage());
}
?>