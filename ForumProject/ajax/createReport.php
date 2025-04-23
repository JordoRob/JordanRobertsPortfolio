<?php
session_start();
include '../../database_mason.php';
if(isset($_SESSION['logged'])){
$heroid=$_SESSION['logged'];
$villainid=$_POST['villainid'];
$focus=$_POST['focus'];
$focusid=$_POST['focusid'];
$report=strip_tags($_POST['report']);
if(strlen($report)<500){
if(strcmp($focus, "account")==0){
    $query="INSERT INTO reports(hero_id,villain_id,focus,report) VALUES (?,?,?,?)";
    $sql = $connection->prepare($query);
        $sql->bind_param("iiss",$heroid,$villainid,$focus,$report);
}
if(strcmp($focus, "post")==0){
    $query="INSERT INTO reports(hero_id,villain_id,focus,report,post_id) VALUES (?,?,?,?,?)";
    $sql = $connection->prepare($query);
    $sql->bind_param("iissi",$heroid,$villainid,$focus,$report,$focusid);
}else if(strcmp($focus, "reply")==0){
    $query0="SELECT replies.post_id,topic_id FROM replies JOIN posts ON replies.post_id=posts.post_id WHERE reply_id=?";
    $sql0 = $connection->prepare($query0);
    $sql0->bind_param("i",$focusid);
    $sql0->execute();
    $result0 = $sql0->get_result(); // get the mysqli result
    $row0 = $result0->fetch_assoc();
    $postid=$row0['post_id'];
    $topicid=$row0['topic_id'];
$query="INSERT INTO reports(hero_id,villain_id,focus,report,reply_id,post_id,topic_id) VALUES (?,?,?,?,?,?,?)";
    $sql = $connection->prepare($query);
    $sql->bind_param("iissiii",$heroid,$villainid,$focus,$report,$focusid,$postid,$topicid);}

    if($sql->execute()){
    
echo json_encode(array("submitted"=>true));}}
}else
echo json_encode(array("submitted"=>false));
?>