<?php
include '../../database_mason.php';
session_start();
if(isset($_POST['table'])&&isset($_POST['term'])){
$table=$_POST['table'];
$term=$_POST['term'];
$compare = $_POST['compare'];
$checked=$_POST['checked'];
$datatype="s";
$bind=true;
if(strcmp($table, "posts")==0){
    $term = "%".$term."%";
    if($checked==1){
    $query="SELECT username,users.user_id,post_id,post_title,content,posts.topic_id,pinned FROM posts LEFT JOIN users ON posts.user_id=users.user_id WHERE ".$compare." LIKE ?";
    }else{
        $query="SELECT username,users.user_id,post_id,post_title,content,posts.topic_id,pinned FROM posts LEFT JOIN users ON posts.user_id=users.user_id WHERE deleted='0' AND ".$compare." LIKE ?";
    }
}else if(strcmp($table, "users")==0){
    $term = "%".$term."%";
    if($checked==1){
    $query="SELECT banned,username,users.user_id,email,COUNT(DISTINCT posts.post_id) as numPosts,COUNT(DISTINCT report_id) as numReports, COUNT(DISTINCT replies.reply_id) as numReplies FROM users LEFT JOIN posts ON users.user_id=posts.user_id LEFT JOIN reports ON users.user_id=reports.villain_id LEFT JOIN replies ON users.user_id=replies.user_id WHERE ".$compare. " LIKE ? GROUP BY user_id;  "; 
}else{
    $query="SELECT banned,username,users.user_id,email,COUNT(DISTINCT posts.post_id) as numPosts,COUNT(DISTINCT report_id) as numReports, COUNT(DISTINCT replies.reply_id) as numReplies FROM users LEFT JOIN posts ON users.user_id=posts.user_id LEFT JOIN reports ON users.user_id=reports.villain_id LEFT JOIN replies ON users.user_id=replies.user_id WHERE banned='0' AND ".$compare. " LIKE ? GROUP BY user_id;  "; 
}
}else if(strcmp($table, "reports")==0){
    $datatype="i";
    if(empty($term)){
        $bind=false;
        if($checked==1){
        $query="SELECT report_id,focus,topic_id,reports.post_id,reports.reply_id,report,heros.username as hero,villains.username as villain,hero_id,villain_id,created_at,resolved FROM reports LEFT JOIN users as heros ON reports.hero_id=heros.user_id LEFT JOIN users as villains ON reports.villain_id=villains.user_id ORDER BY created_at DESC;";
    }else{
        $query="SELECT report_id,focus,topic_id,reports.post_id,reports.reply_id,report,heros.username as hero,villains.username as villain,hero_id,villain_id,created_at,resolved FROM reports LEFT JOIN users as heros ON reports.hero_id=heros.user_id LEFT JOIN users as villains ON reports.villain_id=villains.user_id WHERE resolved='0' ORDER BY created_at DESC;";
    }
}else if($checked==1){
        $query="SELECT report_id,focus,topic_id,reports.post_id,reports.reply_id,report,heros.username as hero,villains.username as villain,hero_id,villain_id,created_at,resolved FROM reports LEFT JOIN users as heros ON reports.hero_id=heros.user_id LEFT JOIN users as villains ON reports.villain_id=villains.user_id WHERE ".$compare."=? ORDER BY created_at DESC;";
    }else{
        $query="SELECT report_id,focus,topic_id,reports.post_id,reports.reply_id,report,heros.username as hero,villains.username as villain,hero_id,villain_id,created_at,resolved FROM reports LEFT JOIN users as heros ON reports.hero_id=heros.user_id LEFT JOIN users as villains ON reports.villain_id=villains.user_id WHERE resolved='0' AND ".$compare."=? ORDER BY created_at DESC;";
}}
    $sql = $connection->prepare($query);
if($bind){
$sql->bind_param($datatype,$term);}
    $sql->execute();
$result = $sql->get_result();
$result=mysqli_fetch_all($result, MYSQLI_ASSOC);
echo json_encode($result);
}
mysqli_close($connection);
?>