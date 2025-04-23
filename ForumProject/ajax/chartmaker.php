<?php
     include '../../database_mason.php';
     if($_POST['check']==0){
        $query='SELECT DISTINCT country, COUNT(DISTINCT ip_address) as num FROM analytics GROUP BY country;';
        $sql = $connection->prepare($query); 
        $sql->execute(); 
        $result = $sql->get_result();
        $data = array();
        foreach ($result as $row) {
            $data[] = $row;
        }
     }else{
        if($_POST['check']==1){
            $query1='SELECT page_url, AVG(TIMESTAMPDIFF(SECOND,entry_time,exit_time)) as timeSec FROM `analytics` WHERE exit_time > 0 GROUP BY page_url;';
            $sql1 = $connection->prepare($query1); 
            $sql1->execute(); 
            $result1 = $sql1->get_result();
            $data = array();
            foreach ($result1 as $row) {
                $data[] = $row;
            }
        }else{
        if($_POST['check']==2){
 $query2='SELECT COUNT(post_id) as numPosts, topic_name FROM posts JOIN topics on posts.topic_id=topics.topic_id GROUP BY topic_name;';
         $sql2 = $connection->prepare($query2); 
         $sql2->execute(); 
         $result2 = $sql2->get_result();
         $data = array();
         foreach ($result2 as $row) {
             $data[] = $row;
         }
        }
     }
     }
     //Best practice is to create a separate file for handling connection to database
        
         
        
         mysqli_close($connection);
         
         echo json_encode($data);
     ?>