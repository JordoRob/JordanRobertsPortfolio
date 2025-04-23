<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
if(isset($_SERVER['REQUEST_METHOD'])){
security_check_and_connect("POST", ['disabled','timerVal']);
terminate(json_encode(set_interval($pdo,$_POST['timerVal'],$_POST['disabled'])));}

function set_interval($pdo,$val,$disabled){
    if($disabled=='true')   //this comes as a string, very annoying
    $disabled=true;
    else
    $disabled=false;


    if($disabled||$val>300||$val<5){    //if its disabled, or a non allowable value, disable it
        $val=0;
    }
$stmt = $pdo->prepare("UPDATE user SET refresh_timer = ? WHERE id = ?");

if ($stmt->execute([$val,$_SESSION['user']])) {
    $_SESSION['timer'] = $val;
    if($disabled){
        return(["Successfully disabled auto-refresh",$val]);
    }else{
    return(["Successfully updated timer to $val seconds",$val]);}
} else {
    return(["Error updating background preference.",301]);
}}
?>