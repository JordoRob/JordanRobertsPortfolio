<?php
include '../../database_mason.php';
session_start();
	$user_id=$_SESSION['logged'];
	$post_id=$_POST['post'];
	$rating=$_POST['rating'];
	$query1 = "SELECT rating FROM rated WHERE post_id=? AND user_id=?";
    $connection->autocommit(false);
    $sql1 = $connection->prepare($query1);
    $sql1->bind_param("ii",$post_id,$user_id);
                        $sql1->execute();
                        $result1 = $sql1->get_result();
                        if(mysqli_num_rows($result1)>0){
                            if($rating==0){
                                $query2="DELETE FROM rated WHERE user_id=? AND post_id=?";
                            $sql2 = $connection->prepare($query2);
                            $sql2->bind_param("ii", $user_id,$post_id);
                            $sql2->execute();
                            }else{
                                $query2="UPDATE rated SET rating=? WHERE user_id=? AND post_id=?";
                                $sql2 = $connection->prepare($query2);
                                $sql2->bind_param("dii",$rating, $user_id,$post_id);
                                $sql2->execute();  
                            }
                        }else{
                            $query2="INSERT INTO rated(user_id,post_id,rating) VALUES (?,?,?)";
                            $sql2 = $connection->prepare($query2);
                            $sql2->bind_param("iid", $user_id,$post_id,$rating);
                            $sql2->execute();
                        }
                        $query3="UPDATE posts SET score=(SELECT SUM(rating) FROM rated WHERE post_id=?) WHERE post_id=?";
                        $sql3 = $connection->prepare($query3);
                        $sql3->bind_param("ii",$post_id,$post_id);
                        $sql3->execute();
                        if($connection->commit()){
                        
                        $query4="SELECT score FROM posts WHERE post_id=?";
                        $sql4 = $connection->prepare($query4);
                        $sql4->bind_param("i",$post_id);
                        $sql4->execute();
                        $result = $sql4->get_result(); // get the mysqli result
                        $row = $result->fetch_assoc(); // fetch data   
echo json_encode($row);
mysqli_close($connection);}
?>