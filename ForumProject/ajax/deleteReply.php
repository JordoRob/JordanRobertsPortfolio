<?php
session_start();
include '../../database_mason.php';
$access=array("allowed"=>false,"completed"=>false);
$replyid=$_POST['replyid'];
if(isset($_SESSION['logged'])){
    $currentUser=$_SESSION['logged'];
    if(isset($_SESSION['adminVer'])){
        if(strcmp($_SESSION['adminVer'],$_SESSION['logged'])==0){
            $access["allowed"]=true;
        }
    }else{
        $query="SELECT user_id FROM replies WHERE reply_id=?;";
       $sql = $connection->prepare($query); 
       $sql->bind_param("i",$replyid);
       $sql->execute();
$result = $sql->get_result();
if(mysqli_num_rows($result)>0){
    $row=$result->fetch_assoc();
        if($currentUser==$row['user_id']){
            $access["allowed"]=true;
    }}}
    if($access['allowed']){
       $query="UPDATE replies SET content='[DELETED]' WHERE reply_id=?;";
       $sql = $connection->prepare($query); 
       $sql->bind_param("i",$replyid);
       if($sql->execute()){
        $access['completed']=true;
       }
    }
}
echo json_encode($access);
mysqli_close($connection);


?>