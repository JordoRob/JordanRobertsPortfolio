<?php
include "../database_mason.php";

$client = $_SERVER['HTTP_USER_AGENT'];
$operatingsystem = explode(";",$client)[1];
$tmp = explode(" ",$client);
$browser="N/A";
$browser = get_browser_name($client);
$browserversion = explode("/",end($tmp))[1];
date_default_timezone_set('America/Vancouver');
$timenow = date("Y-m-d H:i:s");
$today = date("Y-m-d");

if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
 $ip = $_SERVER['HTTP_CLIENT_IP'];
} 
else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
 $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} 
else {
 $ip = $_SERVER['REMOTE_ADDR'];
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://www.geoplugin.net/json.gp?ip=".$ip);
curl_setopt($ch, CURLOPT_HTTPHEADER,  array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
$tmp2= json_decode($result);
$country = $tmp2->geoplugin_countryName;
if($country==null){
    $country="Local or Hidden";
}
curl_close($ch);

$pageurl = strtok($_SERVER["REQUEST_URI"], '?');

if(mysqli_num_rows(mysqli_query($connection,"SELECT id FROM analytics WHERE ip_address='$ip' AND page_url='$pageurl' AND entry_time LIKE '%$today%'")) < 1){
 $query = $connection->prepare("INSERT INTO analytics(page_url,entry_time,ip_address,country,operating_system,browser,browser_version) VALUES(?,?,?,?,?,?,?)");
 $query->bind_param('sssssss',$pageurl,$timenow,$ip,$country,$operatingsystem,$browser,$browserversion);
 $query->execute();
 $query->close();



}

function get_browser_name($user_agent)
{
    if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) return 'Opera';
    elseif (strpos($user_agent, 'Edge')) return 'Edge';
    elseif (strpos($user_agent, 'Chrome')) return 'Chrome';
    elseif (strpos($user_agent, 'Safari')) return 'Safari';
    elseif (strpos($user_agent, 'Firefox')) return 'Firefox';
    elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) return 'Internet Explorer';
    
    return 'Other';
?>