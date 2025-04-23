<?php
include '../../database_mason.php';
$array = array(
    "userCheck" => false,
    "emailCheck" => false,
    "userid"=>0,
);
if(isset($_POST['pass'])&&isset($_POST['user'])&&isset($_POST['email'])&&isset($_POST['pic']))
    if(!empty($_POST['pass'])&&!empty($_POST['user'])&&!empty($_POST['email'])&&!empty($_POST['pic'])){
	$userN=strip_tags($_POST['user']);
	$passWPre=$_POST['pass'];
    $passW=md5($passWPre);
    $email=strip_tags($_POST['email']);
    $pic=$_POST['pic'];

	$query1 = "SELECT username FROM users WHERE username=?";    
    $sql1 = $connection->prepare($query1);
    $sql1->bind_param("s",$userN);
                        $sql1->execute();
                        $result1 = $sql1->get_result();
                        if(mysqli_num_rows($result1)==0){
                            $array["userCheck"]=true;
                        }
                        
$query2 = "SELECT email FROM users WHERE email=?";
$sql2 = $connection->prepare($query2);
$sql2->bind_param("s",$email);
                        $sql2->execute();
                        $result2 = $sql2->get_result();
                        if(mysqli_num_rows($result2)==0){
                            $array["emailCheck"]=true;
                        }

if($array["userCheck"]&&$array["emailCheck"]){

    $query3 = "INSERT INTO users (username, password, email, profile_pic) VALUES (?,?,?,?);";
    $sql3 = $connection->prepare($query3);
    $sql3->bind_param("ssss",$userN, $passW, $email, $pic);
                            $sql3->execute();
                            $userId = $connection -> insert_id;
                             $query4="SELECT username, profile_pic FROM users WHERE user_id=?";
                             $sql4=$connection->prepare($query4);
                             $sql4->bind_param("s",$userId);
                             $sql4->execute();
                             $result4 = $sql4->get_result(); // get the mysqli result
                             $row4 = $result4->fetch_assoc(); // fetch data   
                            session_start();
                            $array['userid']=$userId;
                                 $_SESSION['logged']=$userId;
                               $_SESSION['username']=$row4['username'];
                                $_SESSION['userpic']=$row4['profile_pic'];
}   
    }
echo json_encode($array);
mysqli_close($connection);
?>