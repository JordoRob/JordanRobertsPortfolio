<?php
    session_start();
	include '../../database_mason.php';
    $access = array('success'=>3);
    if(isset($_SESSION['logged'])&&isset($_SESSION['adminVer'])&&isset($_SESSION['admin'])&&isset($_POST['userid'])){
        $access['success']=2;
        $currentid=$_SESSION['logged'];
        $adminid=$_SESSION['adminVer'];
        $banned=$_POST['userid'];
        if($currentid==$adminid&&$_SESSION['admin']){
            $query="SELECT is_admin FROM users where user_id=?";
            $sql = $connection->prepare($query);
            $sql->bind_param("i",$banned);
                                $sql->execute();
                                $result = $sql->get_result();
                                $row= $result->fetch_assoc();
                                if($row['is_admin']!=1){
                                    $query="UPDATE users SET banned=1,user_bio='[USER IS BANNED]',profile_pic='img/user/banned.png' WHERE user_id=?";
                                    $sql = $connection->prepare($query);
                                    $sql->bind_param("i",$banned);
                                $sql->execute();
                                if($_POST['deleteall']==1){
                                    $query="UPDATE posts SET post_title='[DELETED]',content='[USER HAS BEEN BANNED]',deleted=true WHERE user_id=?;";
                                    $sql = $connection->prepare($query);
                                    $sql->bind_param("i",$banned);
                                    $sql->execute();
                                    $query="UPDATE replies SET content='[USER HAS BEEN BANNED]' WHERE user_id=?;";
                                    $sql = $connection->prepare($query);
                                    $sql->bind_param("i",$banned);
                                if($sql->execute())
                                    $access['success']=0;
                                }else{
                                    $access['success']=0;
                                }
                                }else{
                                    $access['success']=1;
                                }
        }
    }
echo json_encode($access);
mysqli_close($connection);


?>