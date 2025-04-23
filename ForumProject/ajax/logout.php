<?php
session_start();
unset($_SESSION['logged']);
unset($_SESSION['username']);
unset($_SESSION['userpic']);
if(isset($_SESSION['admin'])){
unset($_SESSION['admin']);}
if(isset($_SESSION['adminVer'])){
    unset($_SESSION['adminVer']);}
if(isset($_SESSION['logged'])){
echo json_encode(array('success'=>false));
}else{
    echo json_encode(array('success'=>true));
}

?>