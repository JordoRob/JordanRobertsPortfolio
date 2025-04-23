<?php
session_start();
?>
<!DOCTYPE html>
<html>

<head lang="en">
    <meta charset="utf-8">
    <title>Mason's Tavern</title>
    <link rel="stylesheet" href="css/general.css" />
    <link rel="stylesheet" href="css/specific-class.css" />
    <link rel="stylesheet" href="css/specific-id.css" />
    <link rel="stylesheet" href="css/topicpage.css" />
    <link rel="stylesheet" href="css/global.css" />
    <?php
    //specify the server name and here it is localhost
    
include "../database_mason.php";;
include "analyze.php";
$topExists = false;
$top_name = "Who Knows";
if(isset($_GET["id"])){
$topic = strip_tags($_GET["id"]);
$id = intval($topic);
if(isset($_GET['sort'])){
$sort=strip_tags($_GET['sort']);
}else{
$sort='created_at';
}

    if (!$connection) {
        die("Failed " . mysqli_connect_error());
    }
    $query = "SELECT topic_name, topic_img,topic_bio FROM topics where topic_id=?;";
    $sql = $connection->prepare($query);
    $sql->bind_param("i", $id);
    $sql->execute();
    $result = $sql->get_result();
    if ($row = $result->fetch_assoc()) {
        $top_name = ucfirst($row['topic_name']);
        $top_img = $row['topic_img'];
        $topExists = true;
    }}
    ?>
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js'>
        </script>
          
</head>
 <script src='script/joke.js'></script>
<body>
    <div style="overflow:hidden;">
        <div class="scrolltext"><img src="img/shiba.gif" width="40px" style="transform:scaleX(-1)">Checkout the new
            'Fruit' board!<img src="img/shiba.gif" width="40px" style="transform:scaleX(-1)"></div>
    </div>
    <div id="window" class="outerbox">
        <div id="topbar"><img src="img/mason.png" width="28px">
            <p class="header">Mason's Tavern</p>
            <span class="rowflex-utility-main"><a href="#" class="top-buttons"><img src="img/tasks/minimise.png"></a><a href="#" onclick='fullscreen()' class="top-buttons outerbox"><img src="img/tasks/fullscreen.png"></a><a href="#" class="top-buttons outerbox" onclick="joke()">
                    <img src="img/tasks/close.png"></a></span>
        </div>
        <nav><a href="home.php">Home</a><?php 
        if(isset($_SESSION['logged'])&&isset($_SESSION['username'])&&isset($_SESSION['userpic'])){
            echo("<a  href='account.php?id=".$_SESSION['logged']."' id='logAccount' name=".$_SESSION['logged'].">Account</a><a  href='#' id='logout' onclick='logout()'>Logout</a>");
            if(isset($_SESSION['admin'])){
                if($_SESSION['admin']==true){
                    if(isset($_SESSION['adminVer'])){
                        if($_SESSION['adminVer']==$_SESSION['logged']){
                            echo "<a href=admin.php id='adminPortal'>Admin Tools</a>";
                        }
                    }else{
                    echo "<a href=# id='adminPortal' onclick='adminShow()'>Admin Tools</a>";}
                    
                }
            echo("<input type=hidden id='sessionUSERID' value=".$_SESSION['logged']."></input>");
            echo("<input type=hidden id='sessionUSERNAME' value=".$_SESSION['username']."></input>");
            echo("<input type=hidden id='sessionUSERPIC' value=".$_SESSION['userpic']."></input>");
        }}else{
            echo("<a href='#' id='logAccount' onclick='loginShow()'>Login</a>");
        }
        ?></nav>
        <div class="search innerbox">Topic:<span class="innersearch search innerbox">file://<a class="desktop" href="home.php">Home</a><?php
             echo ("/<a class=\"desktop\" style=\"display:inline;\" href=\"#\">" . $top_name . "</a>");
             ?></span>
        </div><div class="search innerbox"><span>Search:<input class="innersearch search innerbox" id='search'></input></div>
        <div class='flexbox'>
        <div class='innerbox rulebox'><h3>RULES</h3>
        <ol>
             <li>Don't be rude!</li>
             <li>No NSFW!</li>
             <li>No spoilers in title!</li>
             <li>Stay on topic, posts in the wrong topic <b>will</b> be removed</li>

        </ol>
        <h3>TAGS</h3><p style='font-size:0.6em;'>Mouse over tags to learn</p>
        <ul>
            <li class='tag discussion'>[Discussion] <br><span class='tagDesc outerbox'>Discussion of anything related to the board</span></li>
            <li class='tag suggestion'>[Suggestion] <br><span class='tagDesc outerbox'>Masons room or Topic-specific quality of life suggestions</span></li>
            <li class='tag question'>[Question] <br><span class='tagDesc outerbox'>Questions about anything and everything involving to the topic</span></li>
            <li class='tag spoiler'>[Spoiler] <br><span class='tagDesc outerbox'>Posts that someone might want to think twice about clicking</span></li>
            <li class='tag admin'>[Admin] <br><span class='tagDesc outerbox'>Posts by your favourite friends down at Mason's Tavern HQ</span></li>
            <li class='tag'><img src='img/tasks/pin.png' width='32px'> <br><span class='tagDesc outerbox'>Posts that are pinned are either very important or very useful!</span></li>
        </ul>
    
    </div>
        <div class="rectangles innerbox" id="main2">
            <?php

            if ($topExists) {
                echo ("<h2 style=\"text-align:center; font-size:2em;\"><img class='mainImage' src=\"" . $top_img . "\" style=\"transform:scaleX(-1)\"><span class='topicTitle'>Welcome to the " . $top_name . " board!</span><img src=\"" . $top_img . "\" class='mainImage'></h2>");
                echo ("<span class='typeDad'><p class='topicBio'>".$row['topic_bio']."</p></span>");
            } else {
                die("Oops this board doesnt exist hehe!!");
            }

            

            if(!in_array($sort,array("created_at","score","title","username","numrep")))
            $sort="created_at";
            $query = "SELECT username,posts.user_id,post_title,posts.post_id,score,posts.created_at,pinned,numrep,profile_pic,tag FROM posts JOIN users ON posts.user_id=users.user_id LEFT JOIN (SELECT post_id, COUNT(reply_id) as numrep FROM replies GROUP BY post_id) as sq1 ON posts.post_id=sq1.post_id WHERE topic_id=? AND deleted=false ORDER BY posts.pinned DESC, $sort DESC;";
            $sql = $connection->prepare($query);
            $sql->bind_param("i", $id);
            $sql->execute();
            $result = $sql->get_result();
            if (mysqli_num_rows($result) > 0) {
                if(isset($_SESSION['logged'])){
                    echo("<a href='newpost.php?topic=".$topic."' class='newpost outerbox'><span>New Post</span></a>");}
                echo ("<table class=\"postTable\" id='postTable'><thead><tr><th></th><th><a href='topic.php?id=".$id."&sort=username'>User</a></th><th>Title</th><th><a href='topic.php?id=".$id."&sort=score'>Score</a></th><th><a href='topic.php?id=".$id."&sort=numrep'>Replies</a></th><th>Rate</th><th></th></tr></thead><tbody class=\"postBody\">");
                foreach ($result as $row) {
                    $pin = $row['pinned'];
                    $replies = $row['numrep'];
                    $score = $row['score'];
                    $title = $row['post_title'];
                    $userid=$row['user_id'];
                    if(strlen($title)>30){
                        $title= substr($title,0,30);
                        $title =$title."...";
                    }
                    if($score==null)
                    $score=0;
                    $date_time = new DateTime($row['created_at']);
                    $date = date_format($date_time, "m/d/Y");
                    $time = date_format($date_time, "h:i A");
                    $tagClass = strtolower(trim($row['tag'],"[]"));
                    if ($replies == null)
                        $replies = 0;
                    echo ("<tr class='post-row' id='".$row['post_id']."'><td style='font-size:0.6em;'>".  $date . " </br>at ".$time. "</td><td class=\"colflex-center\" style=\"font-size:0.8em;font-weight:lighter;\"><img src=\"" . $row['profile_pic'] . "\" class='post-image'>" . $row['username'] ."</td>");
                    if ($pin) {
                        echo ("<td class='titlecol' style=\"color:red\"><img src='img/tasks/pin.png' width='32px' alt='pinned'><a class='inherit searchTitle' href='post.php?post=".$row['post_id']."&topic=".$id."'>" .$row['tag']." " . $row['post_title'] . "</a></td>");
                    } else {
                        echo ("<td class='titlecol'><a class='inherit searchTitle' href='post.php?post=".$row['post_id']."&topic=".$id."'><span class=".$tagClass.">".$row['tag']."</span> ". $row['post_title'] . "</a></td>");
                    }
                    echo("<td class='score'>" . $score . "</td><td>" . $replies . "</td></a>");
                    if(!isset($_SESSION['logged'])){
                    echo("<td class='rating'><span class='ratebuttons-disabled'>Like</span></br><span class='ratebuttons-disabled'>Dislike</span></td><td></td></tr>");
                    }else{
                        $query2 = "SELECT rating FROM rated WHERE post_id=? AND user_id=?";
                        $sql2 = $connection->prepare($query2);
                        $sql2->bind_param("ii", $row['post_id'],$_SESSION['logged']);
                        $sql2->execute();
                        $result2 = $sql2->get_result();
                        if(mysqli_num_rows($result2)>0){
                        $row2 = $result2->fetch_assoc();
                        if($row2['rating']<0){
                            echo("<td class='rating'><span class='ratebuttons-enabled outerbox' onclick='updateScore(this)'>Like</span></br><span class='Dislike ratebuttons-enabled outerbox' onclick='updateScore(this)' selected>Dislike</span></td>");
                        }else{
                            echo("<td class='rating'><span class='Like ratebuttons-enabled outerbox' onclick='updateScore(this)' selected>Like</span></br><span class='ratebuttons-enabled outerbox' onclick='updateScore(this)'>Dislike</span></td>");
                        }
                    }else{
                        echo("<td class='rating'><span class='ratebuttons-enabled outerbox' onclick='updateScore(this)'>Like</span></br><span class='ratebuttons-enabled outerbox' onclick='updateScore(this)'>Dislike</span></td>");
                    }
                    if(($_SESSION['logged']==$userid)||isset($_SESSION['adminVer'])){
                        echo "<td><a href=# class='admin-buttons outerbox' title='Delete Post' onclick='deletePost(".$row['post_id'].")'>X</a></td></tr>";
                    }else echo "<td></td></tr>";
                }
                }
                echo ("</tbody></table>");
            } else {
                echo ("<span class='colflex-center' style='margin-top:10%;'><div>No posts yet! Be the first to post!</div>");
                if(isset($_SESSION['logged'])){
                    echo("<a href='newpost.php?topic=".$topic."' class='newpost outerbox'><span>New Post</span></a></span>");}
            }
            $sql->close();
            ?>
<script>
            function updateScore(button){
                var postid=$(button).parent().parent().attr("id");
                var pressed = $(button).html();
                if(pressed=='Dislike'){
                    var rating=-1;
                }else if(pressed=='Like'){
                    var rating=1;
                }
                    if($(button).attr('class').includes(pressed)){
                        rating=0;
                    }
                    $.ajax({ //Process the form using $.ajax()
            type      : 'POST', //Method type
            url       : 'ajax/updatescore.php', //Your form processing file URL
            data      : {post:postid,rating:rating}, //Forms name
            dataType  : 'json',
            success   : function(data){
                console.log(rating);
                var score=data.score;
                if(data.score==null){
                    score=0;
                }
                if(rating==0){
                    $(button).removeClass(pressed);
                }if(rating==1){
                    $(button).addClass(pressed);
                    var away = $(button).siblings();
                    $(away).removeClass('Dislike');

                }if(rating==-1){
                    $(button).addClass(pressed);
                    var away = $(button).siblings();
                    $(away).removeClass('Like');
                }
                $(button).parent().parent().find(".score").html(score);
            },
            error:  function(jqxhr, status, exception) {
             alert('Exception:', exception);}
        });
    }
</script>
<script>
    $("#search").keyup(function(){
        var filter = $("#search").val().toUpperCase();
  // Declare variables
$(".post-row").each(function(){
    var title = $(this).children().children(".searchTitle");
    console.log(title.text().toUpperCase());
    if(title.text().toUpperCase().includes(filter)){
        $(this).css("display","table-row");
    }else{
        $(this).css("display","none");
    }
});
});
</script>
<script>
$(document).ready(function(){
    $(".tag").mouseenter(function(){
        console.log($(this).text());
        $(this).children(".tagDesc").css("display","block");
    });
    $(".tag").mouseleave(function(){
        $(this).children(".tagDesc").css("display","none");
    });
})

</script>
    
    <script src="script/fullscreen.js"></script>
    <script src="script/deletehandler.js"></script>
    <script src='script/pageExit.js'></script>
        </div>
        </div>
        <span class='rowflex-center footer-box'><span class="innerbox bottombox">Created by Liam and Jordan</span><span class="innerbox bottombox">Photos by; <a href='https://twitter.com/Poloviiinkin'>@Poloviinkin</a> and <a href='https://www.instagram.com/blpixelartist/'>@blpixelartist</a></span></span>
    </div>
    <div ID="joke" style="display:none"><img src="img/mason.png" width="30px"><span
            style="font-size: 14px;font-weight:lighter">Masons Room</span></div>
    <?php include "loginform.php";
    if(isset($_SESSION['admin']))
    if($_SESSION['admin']==true){
        include "adminform.php";}?>
</body>

</html>