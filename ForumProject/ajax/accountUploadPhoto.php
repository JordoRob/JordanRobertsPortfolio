<?php
include '../../database_mason.php';
session_start();
$check=array("access"=>4);
if(isset($_SESSION['logged'])){
    $check["access"]=3;
    $user=$_SESSION['logged'];
    if(file_exists($_FILES['userPicUpload']['tmp_name'])||is_uploaded_file($_FILES['userPicUpload']['tmp_name'])){
        $check["access"]=2;
        if($_FILES['userPicUpload']['size'] < 10485760){
            $relpath="img/user/" . $user . ".png";
            $path=$_SERVER['DOCUMENT_ROOT'].$relpath;
            $photo=$_FILES['userPicUpload'];
            if(file_exists($path)){
            unlink($path);}
            move_uploaded_file($_FILES['userPicUpload']['tmp_name'], "../".$relpath);
        
            $query = "UPDATE users SET profile_pic = ? WHERE user_id=?";
            $sql = $connection->prepare($query);
            $sql->bind_param("si",$relpath,$user);
                                $sql->execute();
                                $result1 = $sql->get_result();
                                $_SESSION['userpic']=$relpath;
                                $check["access"]=0;
        }else{
             $check["access"]=1;
        }
        
        
        
    }
}
echo json_encode($check);
mysqli_close($connection);



?>