<?php
session_start();
$check=array("access"=>2);
if(isset($_SESSION['logged'])){
    $check["access"]=1;
$user=$_SESSION['logged'];
include '../../database_mason.php';
	$oldPre=$_POST['oldPass'];
    $newPre=$_POST['newPass'];
    $oldP=md5($oldPre);
    $newP=md5($newPre);
	$query1 = "SELECT password FROM users WHERE user_id=?";
    $query2 = "UPDATE users SET password = ? WHERE user_id=?";
    $sql = $connection->prepare($query1);
    $sql->bind_param("s",$user);
                        $sql->execute();
                        $result1 = $sql->get_result();
                        $row= $result1->fetch_assoc();
                        if($row["password"]==$oldP){
                            $sql = $connection->prepare($query2);
                            $sql->bind_param("ss",$newP,$user);
                            $sql->execute();
                            $check["access"]=0;
                        }


}
echo json_encode($check);
mysqli_close($connection);



?>