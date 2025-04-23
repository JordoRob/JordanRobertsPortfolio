<?php
session_start();
?>
<!DOCTYPE html>
<html>

<head lang="en">
    <meta charset="utf-8">
    <title>Mason's Tavern</title>
    <?php
    include "../database_mason.php";;
    include "analyze.php";?>
    <link rel="stylesheet" href="css/general.css" />
    <link rel="stylesheet" href="css/specific-class.css" />
    <link rel="stylesheet" href="css/specific-id.css" />
    <link rel="stylesheet" href="css/global.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script>

        function fun(el) {
            var test = el.parentElement.parentElement.nextElementSibling;
            if (test.getAttribute("style") == null){
                test.setAttribute("style", "display:none;");
                el.children[0].src = "img/tasks/fullscreen.png";
            } else {
                test.removeAttribute("style");
                el.children[0].src = "img/tasks/minimise.png";
            }
        }
    </script>
    <script src="script/joke.js"></script>
        <script src='script/replyhandler.js'></script>
     <script src="script/fullscreen.js"></script>

</head>

<body>
    <div style="overflow:hidden;">
        <div class="scrolltext"><img src="img/shiba.gif" width="40px" style="transform:scaleX(-1)">Checkout the new
            'Fruit' board!<img src="img/shiba.gif" width="40px" style="transform:scaleX(-1)"></div>
    </div>
    <div id="window" class="outerbox">
        <div id="topbar"><img src="img/mason.png" width="28px">
            <p class="header">Mason's Tavern</p>
            <span class="rowflex-utility-main"><a href="#" class="top-buttons"><img src="img/tasks/minimise.png"></a><a
                    href="#" class="top-buttons outerbox"><img src="img/tasks/fullscreen.png"></a><a href="#"
                    class="top-buttons outerbox" onclick="joke()">
                    <img src="img/tasks/close.png"></a></span>
        </div>
    
        <?php

        echo ("<nav><a href='home.php'>Home</a>");

        if(isset($_SESSION['logged'])&&isset($_SESSION['username'])&&isset($_SESSION['userpic'])){
            echo("<a  href='account.php?id=".$_SESSION['logged']."' id='logAccount' name=".$_SESSION['logged'].">Account</a><a  href='#' id='logout' onclick='logout()'>Logout</a>");
            if(isset($_SESSION['admin'])){
                if($_SESSION['admin']==true){
                    if(isset($_SESSION['adminVer'])){
                        if($_SESSION['adminVer']==$_SESSION['logged']){
                            echo "<a href=admin.php id='adminPortal'>Admin Tools</a>";
                            echo("<input type=hidden id='adminVer' value=".$_SESSION['logged']."></input>");
                        }
                    }else{
                    echo "<a href=# id='adminPortal' onclick='adminShow()'>Admin Tools</a>";}
                    
                }}
            echo("<input type=hidden id='sessionUSERID' value=".$_SESSION['logged']."></input>");
            echo("<input type=hidden id='sessionUSERNAME' value=".$_SESSION['username']."></input>");
            echo("<input type=hidden id='sessionUSERPIC' value=".$_SESSION['userpic']."></input>");
        }else{
            echo("<a href='#' id='logAccount' onclick='loginShow()'>Login</a>");
        }
    
        ?>
        </nav>
        <div class="search innerbox">Topic:<span class="search innersearch innerbox">file://<a class="desktop"
                    href="home.php">Home/</a><?php
                            if(isset($_GET['post'])&&isset($_GET['topic'])){
                                $topicid=strip_tags($_GET['topic']);
                                $postid=strip_tags($_GET['post']);
                        $query = "SELECT topic_name FROM topics WHERE topic_id=?";
                        $sql = $connection->prepare($query);
                        $sql->bind_param("i",$topicid);
                        $sql->execute();
                        $result = $sql->get_result(); // get the mysqli result
                        $row = $result->fetch_assoc();
                        echo "<a class='desktop' href=topic.php?id=".$topicid.">".ucfirst($row['topic_name'])."/</a><a href=# class='desktop'>Post #".$postid."</a>";
                    ?></span>
        </div>
        <div class='rectangles innerbox' id='main'>
            <div class='work' name=<?php echo $postid; ?>>
            <a href='#focuspost' class='thread-nav-main' title='Jump to main post'></a>
        <div class='post-block' id='focuspost'>
        <?php
            $query = "SELECT username,post_title,score,posts.created_at,content,profile_pic,users.user_id,post_id FROM posts JOIN users ON posts.user_id=users.user_id WHERE posts.post_id=?";
            $sql = $connection->prepare($query);
            $sql->bind_param("i",$postid);
            $sql->execute();
            $result = $sql->get_result(); // get the mysqli result
            $row = $result->fetch_assoc();
            $date_time = new DateTime($row['created_at']);
            $date = date_format($date_time, "m/d/Y h:i A");
            $score = $row['score'];
            if($score==null) $score=0;
            
            echo "<a href='account.php?id=".$row['user_id']."' class='colflex-center post-pic'><img class='post-image' src='".$row['profile_pic']."'><span id='mainUser'>".$row['username']."</span></a>";
            echo "<div class='outerbox posts-main'>";
            echo "<div class='post-top'><span class='post-title'>".$row['post_title']."</span><span>".$date."</span></div>";
            echo "<div class='innerbox posts-body'>";
            echo "<div class='post-content'>".$row['content']."</div>";
            if(isset($_SESSION['logged'])){
                echo "<span class='replyButton outerbox' id='buttonMain' onclick='reply(0)'>Reply</span><span class='replyButton outerbox' id='reportMain' onclick='createReport(this)' data-focus=\"post\" data-focusid='".$row['post_id']."' data-userid='".$row['user_id']."' data-username='".$row['username']."'>Report</span> </div>";
              }else echo "<span class='replyButton innerbox' id='buttonMain' onclick='loginShow()'>Reply</span><span class='replyButton innerbox' id='reportMain' onclick='loginShow()'>Report</span> </div>";
            echo "</div>";
            if(!isset($_SESSION['logged'])){
                echo("<div class='rating'><span class='ratebuttons-disabled'>Like</span></br><span>".$score."</span></br><span class='ratebuttons-disabled'>Dislike</span></div>");
            }else{
                $query2 = "SELECT rating FROM rated WHERE post_id=? AND user_id=?";
                $sql2 = $connection->prepare($query2);
                $sql2->bind_param("ii", $postid,$_SESSION['logged']);
                $sql2->execute();
                $result2 = $sql2->get_result();
                if(mysqli_num_rows($result2)>0){
                    $row2 = $result2->fetch_assoc();
                    if($row2['rating']<0){
                        echo("<div class='rating'><span class='ratebuttons-enabled outerbox' onclick='updateScore(this)'>Like</span></br><span id='scorecount'>".$score."</span></br><span class='Dislike ratebuttons-enabled outerbox' onclick='updateScore(this)' selected>Dislike</span></div>");
                    }else{
                        echo("<div class='rating'><span class='Like ratebuttons-enabled outerbox' onclick='updateScore(this)' selected>Like</span></br><span id='scorecount'>".$score."</span></br><span class='ratebuttons-enabled outerbox' onclick='updateScore(this)'>Dislike</span></div>");
                                }
                }else{
                    echo("<div class='rating'><span class='ratebuttons-enabled outerbox' onclick='updateScore(this)'>Like</span></br><span id='scorecount'>".$score."</span></br><span class='ratebuttons-enabled outerbox' onclick='updateScore(this)'>Dislike</span></div>");
                        }
                        }
                        echo "</div>";
                        echo "<ul class='replies' id='grandpa'>";
                $query="SELECT username, reply_id, content, created_at, profile_pic,users.user_id,banned FROM replies JOIN users ON replies.user_id=users.user_id WHERE post_id=? AND (parent_id IS NULL OR parent_id=0) ORDER BY created_at";
                $sql = $connection->prepare($query);
                $sql->bind_param("i",$postid);
                $sql->execute();
                $largest=0;
                $result = $sql->get_result();
                if (mysqli_num_rows($result) > 0) {
                    foreach($result as $row){
                        if($row['reply_id']>$largest)$largest=$row['reply_id'];
                        $date_time = new DateTime($row['created_at']);
                        $date = date_format($date_time, "m/d/Y h:i A");
                        echo "<li><ul id=".$row['reply_id']." class='replies'>";
                        echo "<div class='posts-reply'>";
                        echo "<div class='innerbox posts-body'>";
                        echo "<span style='font-weight:bold;'><a href='account.php?id=".$row['user_id']."'><img class='reply-image' alt='profile picture' src=".$row['profile_pic']."><span class='reply-username'>".$row['username']." said:</span></a>";
                        echo "<span class=reply-date>".$date."</span></span>";
                        echo "<div class='post-content'>".$row['content']."</div>";
                        if(isset($_SESSION['logged'])){
                          echo "<span class='replyButton outerbox' id='button".$row['reply_id']."' onclick='reply(".$row['reply_id'].")'>Reply</span>";
                            if($row['banned']==0){
                                echo "<span class='replyButton outerbox' id='report".$row['reply_id']."' onclick='createReport(this)' data-focus=\"reply\" data-focusid='".$row['reply_id']."' data-userid='".$row['user_id']."' data-username='".$row['username']."'>Report</span>";
                            }
                            if($_SESSION['logged']==$row['user_id']||isset($_SESSION['adminVer'])){
                               echo "<span class='replyButton outerbox' onclick='DelReply(".$row['reply_id'].")'>Delete</span>";
                            }
                          echo "</div>";
                        }else echo "<span class='replyButton innerbox' id='button".$row['reply_id']."' onclick='loginShow()'>Reply</span><span class='replyButton innerbox' id='report".$row['reply_id']."' onclick='loginShow()'>Report</span> </div>";
                        echo "</div><a href='#".$row['reply_id']."' class='thread-nav-reply'></a></ul></li>";  
                    }
                    echo "<input type=hidden id='biggest' value=".$largest."></input>";
                }}else{echo "Oops! Error Occured!";}
                        ?>
                        </ul>
        </div>
            </div>
            <span class='rowflex-center footer-box'><span class="innerbox bottombox">Created by Liam and Jordan</span><span class="innerbox bottombox">Photos by; <a href='https://twitter.com/Poloviiinkin'>@Poloviinkin</a> and <a href='https://www.instagram.com/blpixelartist/'>@blpixelartist</a></span></span>
            </div>
            
    </div>
    
    <div ID="joke" style="display:none"><img src="img/mason.png" width="30px"><span
            style="font-size: 14px;font-weight:lighter">Masons Room</span></div>
            <?php include "loginform.php";
            include "reportform.php";
    if(isset($_SESSION['admin']))
    if($_SESSION['admin']==true){
        include "adminform.php";}?>
    
    <script src="script/deleteReplyhandler.js"></script>
    <script src='script/joke.js'></script>
     <script src='script/asyncReplies.js'>
     </script>
     <script>
            function updateScore(button){
                var postid=$(".work").attr("name");
                var userid =$("#sessionUSERID").val();
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
                $("#scorecount").html(score);
            },
            error:  function(jqxhr, status, exception) {
             alert('Exception:', exception);}
        });
        }
        </script>  
        <script src='script/pageExit.js'></script>


</body>

</html>