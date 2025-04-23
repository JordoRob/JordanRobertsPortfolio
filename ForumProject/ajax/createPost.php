<?php
session_start();
include '../../database_mason.php';
if(isset($_SESSION['logged'])){
    $user = $_SESSION['logged'];
    $body = $_POST['body'];
    $title = $_POST['title'];
    $topic = $_POST['topic'];
    $tag = $_POST['tag'];
    $funtime=array("[Admin]","[Discussion]","[Question]","[Spoiler]","[Suggestion]");
    $real=false;
    foreach($funtime as $legit){
        if(strcmp($legit,$tag)==0){
            $real=true;
        }
    }
    if(strlen($body)>500||strlen($title)>120||!$real){
        echo json_encode(-1);
    }else{
        $body=strip_tags($body);
        $title=strip_tags($title);
    $query = "INSERT into posts(topic_id,user_id,content,post_title,tag) VALUES (?, ?, ?, ?, ?)";
    $sql = $connection->prepare($query);
    $sql->bind_param("iisss",$topic, $user,$body,$title,$tag);
    $sql->execute();
    $newpost=$sql->insert_id;
    $array =array("id"=> $newpost,
"topic"=>$topic);
    echo json_encode($array);}
}
else
    echo json_encode(-1);
    mysqli_close($connection);
?>