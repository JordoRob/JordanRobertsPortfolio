<?php
$logged= array("access"=>2);
include '../../database_mason.php';
if(isset($_POST['user'])&&isset($_POST['pass'])){
	$userN=$_POST['user'];
	$passWPre=$_POST['pass'];
    $passW=md5($passWPre);
	$query1 = "SELECT password, user_id,is_admin,username,profile_pic,banned FROM users WHERE username=?";
    $sql1 = $connection->prepare($query1);
    $sql1->bind_param("s",$userN);
                        $sql1->execute();
                        $result1 = $sql1->get_result();
                        if(mysqli_num_rows($result1)>0){
                            $row = $result1 -> fetch_assoc();
                            if($row["banned"]==1){
                                $logged["access"]=1;
                            }else{
                                if($passW==$row["password"]){
                                session_start();
                                $_SESSION['logged']=$row["user_id"];
                                $_SESSION['username']=$row["username"];
                                $_SESSION['userpic']=$row["profile_pic"];
                                if($row['is_admin']==1){
                                    $_SESSION['admin']=true;
                                }
                                //setcookie("logged", $row["user_id"], time()+3600);
                                $logged["access"]=0; 
                                }else{
                                }
                            }
                            
                        }}
echo json_encode($logged);
mysqli_close($connection);
?>