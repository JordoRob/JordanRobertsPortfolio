<?php
session_start();
include '../../database_mason.php';
$access=array("allowed"=>false,"completed"=>false);
$postid=$_POST['post_id'];
$pinned=$_POST['pin'];
if(isset($_SESSION['logged'])){
    $currentUser=$_SESSION['logged'];
    if(isset($_SESSION['adminVer'])){
        if(strcmp($_SESSION['adminVer'],$_SESSION['logged'])==0){
            $access["allowed"]=true;
       $query="UPDATE posts SET pinned=? WHERE post_id=?;";
       $sql = $connection->prepare($query); 
       $sql->bind_param("ii",$pinned,$postid);
       if($sql->execute()){
        $access['completed']=true;
       }
    }
}}
echo json_encode($access);
mysqli_close($connection);


?>