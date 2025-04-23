<?php
include '../../database_mason.php';
$query="SELECT COUNT(user_id) as num FROM users WHERE banned=1";
$row=$connection->query($query)->fetch_assoc();
$bannedUsers=$row['num'];
$query="SELECT COUNT(post_id) as num FROM posts WHERE deleted=1";
$row=$connection->query($query)->fetch_assoc();
$deletedPosts=$row['num'];
$query="SELECT COUNT(DISTINCT ip_address) as num FROM analytics";
$row=$connection->query($query)->fetch_assoc();
$uniqueTotal=$row['num'];
$query="SELECT COUNT(DISTINCT ip_address) as num FROM analytics WHERE DATE(created_at)=CURRENT_DATE()";
$row=$connection->query($query)->fetch_assoc();
$uniqueToday=$row['num'];
$query="SELECT COUNT(DISTINCT ip_address) as num FROM analytics WHERE DATE_SUB(CURDATE(),INTERVAL 1 MONTH) <= DATE(created_at)";
$row=$connection->query($query)->fetch_assoc();
$uniqueMonth=$row['num'];
$query="SELECT COUNT(post_id) as num FROM posts WHERE DATE(created_at)=CURRENT_DATE()";
$row=$connection->query($query)->fetch_assoc();
$postsToday=$row['num'];
$query="INSERT INTO stats(deletedPosts,bannedUsers,uniqueToday,uniqueMonth,uniqueTotal,postsToday) VALUES (?,?,?,?,?,?)";
$sql=$connection->prepare($query);
$sql->bind_param("iiiiii",$deletedPosts,$bannedUsers,$uniqueToday,$uniqueMonth,$uniqueTotal,$postsToday);
$sql->execute();
?>