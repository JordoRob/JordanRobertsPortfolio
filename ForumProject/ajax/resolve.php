<?php
include '../../database_mason.php';
session_start();

	if(isset($_SESSION['logged'])&&isset($_SESSION['admin'])){
        if($_SESSION['admin']==true){
            $id=$_POST['id'];
            $switch=$_POST['resolved'];
            if($switch==2){
                $query1 = "DELETE FROM reports WHERE report_id=?";
                $sql1 = $connection->prepare($query1);
                $sql1->bind_param("i",$id);
            }else{
	$query1 = "UPDATE reports SET resolved=? WHERE report_id=?";
    $sql1 = $connection->prepare($query1);
    $sql1->bind_param("ii",$switch,$id);}
if($sql1->execute());
echo json_encode(array("resolved"=>true));   
 }}
mysqli_close($connection);
?>