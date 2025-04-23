<?php
include '../../database_mason.php';
$postid=$_POST['id'];
if(isset($_POST['curRep'])){
$query="SELECT username, reply_id, content, created_at, profile_pic,users.user_id,parent_id,banned FROM replies JOIN users ON replies.user_id=users.user_id WHERE post_id=? AND reply_id>? ORDER BY created_at";
$sql = $connection->prepare($query);
                $sql->bind_param("ii",$postid,$_POST['curRep']);
}else{
    $query="SELECT username, reply_id, content, created_at, profile_pic,users.user_id,parent_id,banned FROM replies JOIN users ON replies.user_id=users.user_id WHERE post_id=? AND parent_id IS NOT NULL ORDER BY created_at";
$sql = $connection->prepare($query);
                $sql->bind_param("i",$postid);
}
$sql->execute();
$result = $sql->get_result();
$result=mysqli_fetch_all($result, MYSQLI_ASSOC);
$test="";
foreach($result as &$row){
    $test=$row['content'];
    $date_time = new DateTime($row['created_at']);
    $date = date_format($date_time, "m/d/Y h:i A");
    $row['created_at']=$date;
}
echo json_encode($result);
mysqli_close($connection);

?>