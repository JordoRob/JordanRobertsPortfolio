<?php
session_start();
$check=array("access"=>1);

$user=$_SESSION['logged'];
include '../../database_mason.php';
    $newBio=strip_tags($_POST['newBio']);
    if(strlen($newBio)<120){
    $query = "UPDATE users SET user_bio = ? WHERE user_id=?";
    $sql = $connection->prepare($query);
    $sql->bind_param("ss",$newBio,$user);
                        $sql->execute();
                        $check["access"]=0;
    }
echo json_encode($check);
mysqli_close($connection);



?>