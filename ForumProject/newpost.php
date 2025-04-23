<?php
session_start();
?>
<!DOCTYPE html>
<html>

<head lang="en">
    <meta charset="utf-8">
    <title>Mason's Tavern</title>
    <?php
    include "../database_mason.php";?>
    <link rel="stylesheet" href="css/general.css" />
    <link rel="stylesheet" href="css/specific-class.css" />
    <link rel="stylesheet" href="css/specific-id.css" />
    <link rel="stylesheet" href="css/global.css" />
    <script>
        function joke() {
            var body = document.getElementById("window");
            body.setAttribute("style", "display:none");
            var icon = document.getElementById("joke");
            icon.setAttribute("style", "display:flex");
            icon.addEventListener("dblclick", reversejoke);
        }
        function reversejoke() {
            var icon = document.getElementById("joke");
            icon.setAttribute("style", "display:none");
            var body = document.getElementById("window");
            body.setAttribute("style", "");

        }
        function fun(el) {
            var test = el.parentElement.parentElement.nextElementSibling;
            if (test.getAttribute("style") == null) {
                test.setAttribute("style", "display:none;")
                el.children[0].src = "img/tasks/fullscreen.png";
            } else {
                test.removeAttribute("style");
                el.children[0].src = "img/tasks/minimise.png";
            }
        }
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
            <span class="rowflex-utility-main"><a href="#" class="top-buttons"><img src="img/tasks/minimise.png"></a><a
                    href="#" onclick="fullscreen()" class="top-buttons outerbox" id="screenchange"><img src="img/tasks/fullscreen.png"></a><a href="#"
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
                        }
                    }else{
                    echo "<a href=# id='adminPortal' onclick='adminShow()'>Admin Tools</a>";}
                    
                }
            echo("<input type=hidden id='sessionUSERID' value=".$_SESSION['logged']."></input>");
            echo("<input type=hidden id='sessionUSERNAME' value=".$_SESSION['username']."></input>");
            echo("<input type=hidden id='sessionUSERPIC' value=".$_SESSION['userpic']."></input>");
            
        }
        $userid=$_SESSION['logged'];
    }else{
            echo("<a href='#' id='logAccount' onclick='loginShow()'>Login</a>");
        }
        
        ?>
        </nav>
        <div class="search innerbox">Topic:<span class="search innersearch innerbox">file://<a class="desktop"
                    href="home.php">Home</a>/New Post</span>
        </div>
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
        <div class="rectangles innerbox" id="main2" style="overflow:hidden;">
        <?php
        if(isset($_SESSION['logged'])){
            echo "<h2 class='title'><img src='img/espeon.webp' width='50px' style='transform:scaleX(-1)'>Create Post<img src='img/espeon.webp' width='50px'></h2>";
            
           echo "<span class='colflex-center'>";
                echo "<span class='colflex-center postbox outerbox'>";
                
                echo "<div class='top' style='width: 99%;'><span>New Post</span><span class='rowflex-utility'><a href='#' onclick='fun(this)'";
                                echo " class='top-buttons'><img src='img/tasks/minimise.png'></a></span></div>";
                                
                              echo "<form> <label for='postTitle' id='titleLabel'>Title: </label><input type='text' class='innerbox' id='postTitle'><label for='postTitle' id='titleLength'>120</label>";
                              echo "<label style='margin-left:24px;' for='topics'>Topic: </label>";

                              echo "<select class='topicSelect' name='topics' id='topics'>";
                              
                              $query = "SELECT * FROM topics";
            $sql = mysqli_query($connection, $query);
            if(isset($_GET['topic'])){
            $topic=$_GET['topic'];}else $topic=1;
            foreach($sql as $row){
                echo "<option value=".$row['topic_id'];
                if($topic==$row['topic_id']){
                    echo " selected>".ucfirst($row['topic_name'])."</option>";
                }else{
                    echo ">".ucfirst($row['topic_name'])."</option>";
                }
            }
                echo "</select><label style='margin-left:24px;' for='tags'>Tag: </label>";
                echo "<select class='tagSelect topicSelect' name='tags' id='tags'>";
                if(isset($_SESSION['admin'])){
                    if($_SESSION['admin']==true){
                        echo "<option value='[Admin]' class='tagAdmin'>[Admin]</option>";
                    }
                }
                echo "<option value='[Discussion]' class='tagDiscussion'>[Discussion]</option>";
                echo "<option value='[Question]' class='tagQuestion'>[Question]</option>";
                echo "<option value='[Suggestion]' class='tagSuggestion'>[Suggestion]</option>";
                echo "<option value='[Spoiler]' class='tagSpoiler'>[Spoiler]</option></select> ";
                $query="SELECT username, profile_pic FROM users WHERE user_id=?";
                $sql=mysqli_prepare($connection,$query);
                $sql->bind_param("i",$userid);
                $sql->execute();
                $result = $sql->get_result(); // get the mysqli result
                $row = $result->fetch_assoc(); // fetch data  
                 echo "</br><img class='mainImage' src='".$row['profile_pic']."'><label for='post' id='postLabel'> ".$row['username']." said: </label></br><textarea id='post' class='innerbox' rows='6' cols='80'></textarea>";
                echo "<label for='post' id='postLength'>500</label></br><input class='postButton outerbox' type='submit' id='postSubmit' value='Post'>";
                echo "<span id='incorrect' style='color:red;display:none;'>Fill out entire form!</span></form>";
               echo "</span></span>";}
               else{
                echo "<h2 class='title'><img src='img/espeon.webp' width='50px' style='transform:scaleX(-1)'>How did you get here? Go log in to post :(<img src='img/espeon.webp' width='50px'></h2>";
               }
                ?>
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    
    <script src="registerhandler.js"></script>
             <script src="script/fullscreen.js"></script>
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
<script>
$(document).ready(function(){
    $("#postSubmit").click(function(){
        event.preventDefault();
        var sub = true;
        if($("#postTitle").val()==""){
            $("#titleLabel").css("color","red");
            sub=false;
            $("#incorrect").css("display","inline");
            $("#postTitle").keyup(function(){
                $("#titleLabel").css("color","black");
                $("#incorrect").css("display","none");
            })
        }else if($("#postTitle").val().length>120){
            sub=false;
            $("#postTitle").keyup(function(){
                if($("#postTitle").val().length<121){
                $("#titleLengt").css("color","black");}})
        }
        
        
        if($("#post").val()==""){
            $("#postLabel").css("color","red");
            sub=false;
            $("#incorrect").css("display","inline");
            $("#post").keydown(function(){
                $("#postLabel").css("color","black");
                $("#incorrect").css("display","none");
            })
        }else if($("#post").val().length>500){
            sub=false;
            $("#post").keyup(function(){
                if($("#post").val().length<500){
                $("#postLength").css("color","black");}
            })
        }
        
        if(sub){
            var title=$("#postTitle").val();
            var body=$("#post").val();
            var topic=$("#topics").val();
            var tag=$("#tags").val();
            $.ajax({ //Process the form using $.ajax()
            type      : 'POST', //Method type
            url       : 'ajax/createPost.php', //Your form processing file URL
            data      : {title:title,body:body,tag:tag,topic:topic}, //Forms name
            dataType  : 'json',
            success   : function(data){
               if(data.id>0)
               window.location.href="post.php?post="+data.id+"&topic="+data.topic;
               else{
                alert("It's too big UwU!!!");
               }
            },error: function(){
                alert("Unknown Error");
            }
        })
    }});
    $("#post").keyup(function(){
        typed=$("#post").val().length;
        $("#postLength").text(500-typed);
        if(typed>500){
            $("#postLength").css("color","red");
        }else{
            $("#postLength").css("color","black");
        }
    });
    $("#postTitle").keyup(function(){
        typed=$("#postTitle").val().length;
        $("#titleLength").text(120-typed);
        if(typed>120){
            $("#titleLength").css("color","red");
        }else{
            $("#titleLength").css("color","black");
        }
    });
});
</script>
</body>
</html>