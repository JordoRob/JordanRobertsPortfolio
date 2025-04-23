<?php
include '../../database_mason.php';
date_default_timezone_set('America/Vancouver');
$timenow = date('Y-m-d H:i:s');
$today = date('Y-m-d');
echo $_GET["pageurl"];
if(isset($_GET["pageurl"])){    
 $pageurl = $_GET["pageurl"];

 if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
  $ip = $_SERVER['HTTP_CLIENT_IP'];
 } 
 else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
  $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
 } 
 else {
  $ip = $_SERVER['REMOTE_ADDR'];
 }
 $pageurl="%".$pageurl."%";
 $query = $connection->prepare("UPDATE analytics SET exit_time = ? WHERE ip_address= ? AND page_url LIKE ? AND DATE(entry_time) = CURRENT_DATE()");
 $query->bind_param('sss',$timenow,$ip,$pageurl);
 $query->execute();
 $query->close();
}
?>
