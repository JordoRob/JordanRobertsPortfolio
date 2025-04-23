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
    <link rel="stylesheet" href="css/global.css" />
   <?php
    //specify the server name and here it is localhost
    include "analyze.php";
include "../database_mason.php";


$userName = "Somebody";
$userExists = false;
$ownAccount=false;
$profilePic = 'img/tasks/banned.png';
$bio = 'This person doesn\'t exist';
$userid=0;
$banned=false;
if(isset($_GET["id"])){
    $userid = strip_tags($_GET["id"]);
    
    if (!$connection) {
        die("Failed " . mysqli_connect_error());
    }
    $query = "SELECT username, profile_pic, user_bio, banned FROM users where user_id=?;";
    $sql = $connection->prepare($query);
    $sql->bind_param("i", $userid);
    $sql->execute();
    $result = $sql->get_result();
    if ($row = $result->fetch_assoc()) {
        $userName = ($row['username']);
        $profilePic = $row['profile_pic'];
        $bio = $row['user_bio'];
        $userExists = true;
        if ($row['banned']==1){
            $banned=true;
        }
   }
 
   if(isset($_SESSION['logged'])){
    if($_SESSION['logged']==$userid){
    $ownAccount=true;
    }   }
   }
   
    ?>
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js'>
        </script>
</head>
   
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
                
            }
            echo("<input type=hidden id='sessionUSERID' value=".$_SESSION['logged']."></input>");
            echo("<input type=hidden id='sessionUSERNAME' value=".$_SESSION['username']."></input>");
            echo("<input type=hidden id='sessionUSERPIC' value=".$_SESSION['userpic']."></input>");
        }else{
            echo("<a href='#' id='logAccount' onclick='loginShow()'>Login</a>");
        }
        ?></nav>
        <div class="search innerbox">Topic:<span class="innersearch search innerbox">file://<a class="desktop" href="home.php">Home</a><?php
             echo ("/<a class=\"desktop\" style=\"display:inline;\" href=\"#\">". $userName . "</a>");
             ?></span>
        </div>
        <div class='flexbox'>
        <div class='innerbox accountbox'>
        </br>
        <h3><?php echo $userName ?></h3>
        
        </br>
        <img style="max-width:100px;max-height:100px;" src= <?php echo $profilePic ?>>
        <?php if($ownAccount){

            echo "<form style='margin:6px;' id='imageForm' method='post' enctype='multipart/form-data'><label style='font-size:.7em; width:95%;' class='outerbox ratebuttons-enabled accountPagePic'><input style='display:none;' id='userPicUpload' type='file' accept='image/*' name='userPicUpload'>Change Profile Picture</label></br><input style='font-size:.5em;' class='outerbox ratebuttons-enabled' type='submit' value='Upload' id='uploadButton'</form></br>";

       }?>
        </br>
       <?php echo "<div style='width: 99%; overflow:auto; word-wrap:break-word;' id='accountBioCurrent'>$bio</div>";
       
       if($ownAccount){
        echo "<form id='bioForm' method='post'><textarea id='newBioInput' style='display:none;' type='text' maxlength='120' value='" . $bio . "'></textarea></form>";
        echo "<button id='accountBioChangeButton' style='font-size:.7em; width:65%; height:30px; font-size:.7em;' class='outerbox ratebuttons-enabled'>Change Bio</button>";
        echo "<button id='submitAccountBioChangeButton' style='font-size:.7em; width:65%; height:30px; font-size:.7em; display:none;' class='outerbox ratebuttons-enabled'>Submit</button>";
        
        echo "</br></br></br></br></br></br><button class='outerbox ratebuttons-enabled' onClick='pwChangeShow()' type='button' style='padding:4px 8px;width:90%;' class='outerbox' id='pwChange'>Change Password</button>";
        echo "<button  class='outerbox ratebuttons-enabled' onClick='emailChangeShow()' type='button' style='margin-top:5px;margin-bottom:5px;padding:4px 8px;width:auto;' class='outerbox' id='emailChange'>Change Email</button>";
        }else{
            if($userExists)
           echo "<span class='replyButton outerbox' onclick='createReport(this)' data-focus=\"account\" data-focusid='".$userid."' data-userid='".$userid."' data-username='".$userName."'>Report</span>";
        }
       ?>
    </div>
        
    <div class="rectangles innerbox" id="main2">
            <?php
            if ($banned){
                echo ("<h2 style=\"text-align:center; font-size:2em;\"><img class='mainImage' src=\"\" style=\"transform:scaleX(-1)\"><span class='topicTitle'>Welcome to " . $userName . "'s room!</span><img src=\"\" class='mainImage'></h2>");

                echo "<h1 style='color:red;text-align:center;'>BANNED</h1>";
                
            }else{
            if ($userExists) {
                    echo ("<h2 style=\"text-align:center; font-size:2em;\"><img class='mainImage' src=\"\" style=\"transform:scaleX(-1)\"><span class='topicTitle'>Welcome to " . $userName . "'s room!</span><img src=\"\" class='mainImage'></h2>");
                    echo("<button id='showRepliesandPosts' style='font-size:.7em; width:15%; height:40px; font-size:.7em;' class='outerbox ratebuttons-enabled' onclick='showReplies()'>Show Replies</button>");
                } else {
                   die("Oops this person doesnt exist hehe!!");
                 }
            }
            

            ?>
            <div id="replies" style="display:none;">
            <?php
                 $query="SELECT topics.topic_id, topic_name, username, reply_id, replies.content, replies.created_at, profile_pic,users.user_id,banned,replies.post_id FROM replies JOIN users ON replies.user_id=users.user_id JOIN posts ON replies.post_id=posts.post_id JOIN topics ON posts.topic_id=topics.topic_id WHERE username=? ORDER BY created_at;";
                 $sql = $connection->prepare($query);
                $sql->bind_param("s",$userName);
                $sql->execute();
                $result = $sql->get_result();
                if (mysqli_num_rows($result) > 0) {
                    echo ("<table class=\"accountTable\" id='accountTable'><thead><tr><th></th><th>User</th><th>Content</th><th>Board");
                    
                        echo("</th></tr></thead><tbody class=\"accountBody\">");
                    
                    foreach ($result as $row) {
                        $topicName = $row['topic_name'];
                        $boardId = $row['topic_id'];
                        $username = $row['username'];
                        $content = $row['content'];
                        $pPic = $row['profile_pic'];
                        $postId = $row['post_id'];
                        $topicId = $row['topic_id'];

                        if(strlen($content)>30){
                            $content= substr($content,0,50);
                            $content =$content."...";
                        }

                        $date_time = new DateTime($row['created_at']);
                        $date = date_format($date_time, "m/d/Y");
                        $time = date_format($date_time, "h:i A");
                        echo ("<tr class='account-row' id='".$postId."'><td style='font-size:0.6em;'>".  $date . " </br>at ".$time. "</td><td class=\"colflex-center\" style=\"font-size:0.8em;font-weight:lighter;\"><img src=\"" . $pPic . "\" width=\"30px\">" . $userName ."</td>");
                        echo ("<td class='titlecol'><a class='inherit searchTitle' href='post.php?post=".$postId."&topic=".$topicId."'>" . $content . "</a></td>");
                        echo("<td>" . $topicName . "</td>");
 
                            echo ("</tr>");
                    }
                    echo ("</tbody></table>");
                } else {
                    echo ("$userName hasn't made any replies...");
                }
            ?>
            </div>

            <div id="posts">
            <?php
            $query = "SELECT username,post_title,posts.post_id,score,posts.created_at,pinned,numrep,profile_pic,tag,posts.topic_id,topic_name,deleted FROM posts JOIN users ON posts.user_id=users.user_id LEFT JOIN (SELECT post_id, COUNT(reply_id) as numrep FROM replies GROUP BY post_id) as sq1 ON posts.post_id=sq1.post_id JOIN topics ON posts.topic_id=topics.topic_id WHERE posts.user_id=? ORDER BY posts.created_at DESC;";
            $sql = $connection->prepare($query);
            $sql->bind_param("i", $userid);
            $sql->execute();
            $result = $sql->get_result();
            if (mysqli_num_rows($result) > 0) {
                echo ("<table class=\"accountTable\" id='accountTable'><thead><tr><th></th><th>User</th><th>Title</th><th>Board</th><th>Score</th><th>Replies");
                if($userExists&&$ownAccount){
                    echo("</th><th>Delete</th></tr></thead><tbody class=\"accountBody\">");
                }else{
                    echo("</th></thead></tr><tbody class=\"accountBody\">");
                }
                foreach ($result as $row) {
                    $pin = $row['pinned'];
                    $replies = $row['numrep'];
                    $score = $row['score'];
                    $title = $row['post_title'];
                    $topic = ucfirst($row['topic_name']);
                    $isDeleted = $row['deleted'];
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
                    echo ("<tr class='account-row' id='".$row['post_id']."'><td style='font-size:0.6em;'>".  $date . " </br>at ".$time. "</td><td class=\"colflex-center\" style=\"font-size:0.8em;font-weight:lighter;\"><img src=\"" . $row['profile_pic'] . "\" width=\"30px\">" . $row['username'] ."</td>");
                    echo ("<td class='titlecol'><a class='inherit searchTitle' href='post.php?post=".$row['post_id']."&topic=".$row['topic_id']."'><span class=".$tagClass.">".$row['tag']."</span> ". $row['post_title'] . "</a></td>");
                    echo("<td>" . $topic . "</td>");
                    echo("<td class='score'>" . $score . "</td><td>" . $replies . "</td>");

                    if($userExists&&$ownAccount){
                        if($isDeleted){
                            echo ("<td><a href=# class='admin-buttons innerbox' title='Delete Post'>X</a>");
                        }else{
                        echo ("<td><a href=# class='admin-buttons outerbox' title='Delete Post' onclick='deletePost(".$row['post_id'].")'>X</a>");
                        }
                        
                    }else{
                        echo ("</tr>");
                    }
                }
                echo ("</tbody></table>");
            } else {
                echo ("$userName hasn't made any posts...");
            }
            $sql->close();
            ?>
            </div>
<script>

</script>
    
    <script src="script/accountChangeHandler.js"></script>
    <script src="script/fullscreen.js"></script>
    <script src="script/accountDeletehandler.js"></script>
    <script src="script/joke.js"></script>
    <script src='script/pageExit.js'></script>
    <script>
       
       function showPosts(){
            document.getElementById("posts").style.display="block";
            document.getElementById("replies").style.display="none";
            document.getElementById("showRepliesandPosts").onclick=showReplies;
            document.getElementById("showRepliesandPosts").innerHTML="Show Replies";
        }
        function showReplies(){
            document.getElementById("posts").style.display="none";
            document.getElementById("replies").style.display="block";
            document.getElementById("showRepliesandPosts").onclick=showPosts;
            document.getElementById("showRepliesandPosts").innerHTML="Show Posts";
        }



    </script>
        </div>
        </div>
        <span class='rowflex-center footer-box'><span class="innerbox bottombox">Created by Liam and Jordan</span><span class="innerbox bottombox">Photos by; <a href='https://twitter.com/Poloviiinkin'>@Poloviinkin</a> and <a href='https://www.instagram.com/blpixelartist/'>@blpixelartist</a></span></span>
    </div>
    <?php include "loginform.php";
    include "reportform.php";
    if(isset($_SESSION['admin']))
    if($_SESSION['admin']==true){
        include "adminform.php";}?>
            <div ID="joke" style="display:none"><img src="img/mason.png" width="30px"><span
            style="font-size: 14px;font-weight:lighter">Masons Room</span></div>
</body>
<div class="accountChangePopup outerbox"id="changePw">
        <div class="top"><span>Change password</span><span class="rowflex-utility"><a href="#" onclick="pwChangeClose()"
                                    class="top-buttons"><img src="img/tasks/close.png"></a></span></div>

            <div class="accountChangeForm">
                    <label for="oldP"><b>Old Password:</b></label>
                    <input id='oldp' type="text" name="oldP" required>
        </br>
                    <label for="newp"><b>New Password:</b></label>
                    <input id='newpass' type="text" name="newp" required>
            
        </br>
                    <label for="newPVerify"><b>Confirm Password:</b></label>
                    <input id='newpassverify' type="text" name="newPVerify" required>
        </br>
                    <button class="passwordButton outerbox" type="submit">Change</button>
                    <span id="passwordMatchError" style="display:none;">Passwords do not match.</span>
                    <span id="AccountpStrength" style="display:none;">Password must be at least eight characters and include one capital, one number and one special character. </span>
        </div>
    </div>
    <div class="accountChangePopup outerbox" id="changeEmail">
        <div class="top"><span>Change email</span><span class="rowflex-utility"><a href="#" onclick="emailChangeClose()"
                                    class="top-buttons"><img src="img/tasks/close.png"></a></span></div>

            <div class="accountChangeForm">
                    <label for="oldEmail"><b>Old Email:</b></label>
                    <input style="margin-left:13px;" id='oldEmail' type="text" name="oldEmail" required>
        </br>
                    <label for="newEmail"><b>New Email:</b></label>
                    <input id='newEmail' type="text" name="newEmail" required>
        </br>
                    <label for="pass"><b>Password:</b></label>
                    <input style="margin-left:5px;" id='emailPassword' type="password" name="pass" required>
            
        </br>
                    <button class="emailButton outerbox" type="submit">Change</button>
                    <span id="emailMatchError" style="display:none;">New email invalid.</span>
        </div>
    </div>
</html>