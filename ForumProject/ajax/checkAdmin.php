<?php
$logged= array('access'=>false);
include '../../database_mason.php';
session_start();

	if(isset($_SESSION['logged'])&&isset($_SESSION['admin'])){
        if($_SESSION['admin']==true){
    $userid=$_SESSION['logged'];
	$passW=$_POST['pass'];
	$query1 = "SELECT admin_pass FROM admins WHERE user_id=?";
    $sql1 = $connection->prepare($query1);

    $sql1->bind_param("i",$userid);
                        $sql1->execute();
                        $result1 = $sql1->get_result();
                        if(mysqli_num_rows($result1)>0){
                            $row = $result1 -> fetch_assoc();
                            if(strcmp($passW,$row["admin_pass"])==0){
                                $_SESSION['adminVer']=$_SESSION['logged'];
                                $logged = array('access' => true); 
                            }}
                        }}
echo json_encode($logged);
mysqli_close($connection);
?>