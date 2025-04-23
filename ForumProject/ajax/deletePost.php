<?php
session_start();
include '../../database_mason.php';
$access=array("allowed"=>false,"completed"=>false);
$postid=$_POST['post_id'];
if(isset($_SESSION['logged'])){
    $currentUser=$_SESSION['logged'];
    if(isset($_SESSION['adminVer'])){
        if(strcmp($_SESSION['adminVer'],$_SESSION['logged'])==0){
            $access["allowed"]=true;
        }
    }else{
        $query="SELECT user_id FROM posts WHERE post_id=?;";
       $sql = $connection->prepare($query); 
       $sql->bind_param("i",$postid);
       $sql->execute();
$result = $sql->get_result();
if(mysqli_num_rows($result)>0){
    $row=$result->fetch_assoc();
        if($currentUser==$row['user_id']){
            $access["allowed"]=true;
    }}}
    if($access['allowed']){
       $query="UPDATE posts SET post_title='[DELETED]',content='[DELETED]',deleted=true WHERE post_id=?;";
       $sql = $connection->prepare($query); 
       $sql->bind_param("i",$postid);
       if($sql->execute()){
        $access['completed']=true;
       }
    }
}
echo json_encode($access);
mysqli_close($connection);


?>