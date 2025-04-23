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
    <script>
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
    <script src='script/joke.js'></script>
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
        echo ("<nav><a href='#'>Home</a>");
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
        
        ?>
        </nav>
        <div class="search innerbox">Topic:<span class="search innersearch innerbox">file://<a class="desktop"
                    href="#">Home</a></span>
        </div>
        <div class="rectangles innerbox" id="main">
            <h2 class="title"><img src="img/espeon.webp" width="50px" style="transform:scaleX(-1)">Welcome to Mason's
                Tavern!<img src="img/espeon.webp" width="50px"></h2>
                <span class='post-blurb'><p>Mason's Tavern was created by Liam Stienstra and Jordan Roberts in 2023 as a project for COSC 360 Web Programming,
                    the site purposefully attempts to mimic the aestheics of a 90's era operating system. The site operates as a forum page for users to post and reply to topics. The site is built using PHP, HTML, CSS, and Javascript. 
                    The site uses a MySQL database to store user information, posts, replies, and topics.
                </p>
                <a class="replyButton outerbox" href="../../index.html">Return to Main Page</a>
            </span>
                
            <div class='topic-select'>
                <span class="colflex-center explorer outerbox">
                    <div class="top"><span>General </span><span class="rowflex-utility"><a href="#" onclick="fun(this)"
                                class="top-buttons"><img src="img/tasks/minimise.png"></a></span></div>
                    <div class="rectangles colflex-left innerbox">
                    <a href="topic.php?id=14" class="desktop rowflex">
                            <span class="hoverimg"><img src="img/directory_close.png" id="folder"><img
                                    src="img/topics/general.png" class="icons"></span><span class="topic">General</span></a>
                        <a href="topic.php?id=4" class="desktop rowflex">
                            <span class="hoverimg"><img src="img/directory_close.png" id="folder"><img
                                    src="img/topics/news.png" class="icons"></span><span class="topic">News</span></a>
                        <a href="topic.php?id=3" class="desktop rowflex">
                            <span class="hoverimg"><img src="img/directory_close.png" id="folder"><img
                                    src="img/topics/canada.png" class="icons"
                                    ></span><span
                                class="topic">Canada</span></a>
                        <a href="topic.php?id=2" class="desktop rowflex">
                            <span class="hoverimg"><img src="img/directory_close.png" id="folder"><img src="img/topics/usa.png"
                                    class="icons"></span><span class="topic">USA</span></a>

                    </div>
                </span>

                <span class="colflex-center explorer outerbox">
                    <div class="top">Hobbies<span class="rowflex-utility"><a href="#" onclick="fun(this)"
                                class="top-buttons"><img src="img/tasks/minimise.png"></a></span></div>
                    <div class="rectangles colflex-left innerbox">
                        <a href="topic.php?id=11" class="desktop rowflex">
                            <span class="hoverimg"><img src="img/directory_close.png" id="folder"><img
                                    src="img/topics/cars.png" class="icons"></span><span
                                class="topic">Cars</span></a>
                        <a href="topic.php?id=1" class="desktop rowflex">
                            <span class="hoverimg"> <img src="img/directory_close.png" id="folder">
                                <img src="img/topics/fruit.png" class="icons"></span><span class="topic">Fruit</span></a>
                        <a href="topic.php?id=7" class="desktop rowflex">
                            <span class="hoverimg"> <img src="img/directory_close.png" id="folder">
                                <img src="img/topics/alcohol.png" class="icons"></span><span
                                class="topic">Alcohol</span></a>
                        <a href="topic.php?id=15" class="desktop rowflex">
                            <span class="hoverimg"> <img src="img/directory_close.png" id="folder">
                                <img src="img/topics/diy.png" class="icons"
                                    ></span><span class="topic">DIY</span></a>

                    </div>
                </span>
                <span class="colflex-center explorer outerbox">
                    <div class="top">Pop Culture<span class="rowflex-utility"><a href="#" onclick="fun(this)"
                                class="top-buttons"><img src="img/tasks/minimise.png"></a></span></div>
                    <div class="rectangles colflex-left innerbox">
                        <a href="topic.php?id=12" class="desktop rowflex">
                            <span class="hoverimg"><img src="img/directory_close.png" id="folder"><img
                                    src="img/topics/music.png" class="icons"></span><span class="topic">Music</span></a>
                        <a href="topic.php?id=13" class="desktop rowflex">
                            <span class="hoverimg"><img src="img/directory_close.png" id="folder"><img src="img/topics/tv.png"
                                    class="icons"></span><span class="topic">TV</span></a>
                        <a href="topic.php?id=6" class="desktop rowflex">
                            <span class="hoverimg"><img src="img/directory_close.png" id="folder"><img
                                    src="img/topics/movies.png" class="icons"></span><span class="topic">Movies</span></a>
                        <a href="topic.php?id=5" class="desktop rowflex">
                            <span class="hoverimg"><img src="img/directory_close.png" id="folder"><img
                                    src="img/topics/anime.png" class="icons"></span><span class="topic">Anime</span></a>

                    </div>
                </span>
                <span class="colflex-center explorer outerbox">
                    <div class="top">Professional<span class="rowflex-utility"><a href="#" onclick="fun(this)"
                                class="top-buttons"><img src="img/tasks/minimise.png"></a></span></div>
                    <div class="rectangles colflex-left innerbox">
                        <a href="topic.php?id=8" class="desktop rowflex">
                            <span class="hoverimg"><img src="img/directory_close.png" id="folder"><img
                                    src="img/topics/business.png" class="icons"></span><span class="topic">Business</span></a>
                        <a href="topic.php?id=9" class="desktop rowflex">
                            <span class="hoverimg"><img src="img/directory_close.png" id="folder"><img
                                    src="img/topics/software.png" class="icons"></span><span
                                class="topic">Software</span></a>
                        <a href="topic.php?id=10" class="desktop rowflex">
                            <span class="hoverimg"><img src="img/directory_close.png" id="folder"><img
                                    src="img/topics/wallstreet.png" class="icons"></span><span class="topic">Wall
                                Street</span></a>
                    </div>
                </span>
                
            </div>
            <span class='rowflex-center'>
            <div class='outerbox trend-test'><h3>Trending Posts</h3>
                    <table class='trending'> 
                        <thead><tr> 
                            <th>Topic</th><th>User</th><th>Title</th><th>Score</th><th>Replies</th></tr></thead>
                            <tbody>
                    <?php
                        include "../database_mason.php";
                        include "analyze.php";
                        $query='SELECT posts.post_id, post_title, username, topic_name, score,posts.topic_id,posts.user_id,numrep,topic_img,profile_pic FROM posts JOIN users on posts.user_id=users.user_id JOIN topics ON posts.topic_id=topics.topic_id LEFT JOIN (SELECT post_id, COUNT(reply_id) as numrep FROM replies GROUP BY post_id) as sq1 ON posts.post_id=sq1.post_id WHERE deleted=0 ORDER BY score DESC,numrep DESC, created_at DESC LIMIT 5';
                        $result = $connection -> query($query);
                        foreach($result as $row){
                            $title=$row['post_title'];
                            $rep=$row['numrep'];
                            if($row['numrep']==null)
                                $rep=0;
                            if(strlen($title)>20){
                                $title= substr($title,0,20);
                                $title =$title."...";
                            }
                            echo "<tr>";
                            echo "<td><a href='topic.php?id=".$row['topic_id']."'><img class='icons2' src='".$row['topic_img']."'>".ucfirst($row['topic_name'])."</a></td>";
                            echo "<td><a href='account.php?id=".$row['user_id']."'><img class='icons2' src='".$row['profile_pic']."'>".$row['username']."</a></td>";
                            echo "<td><a href='post.php?post=".$row['post_id']."&topic=".$row['topic_id']."'>".$title."</a></td>";
                            echo "<td>".$row['score']."</td>";
                            echo "<td>".$rep."</td>";
                            echo "</tr>";
                        }
                    ?>
                    </tbody></table>
                </div>
                    </span>
        </div>
        <span class='rowflex-center footer-box'><span class="innerbox bottombox">Created by Liam and Jordan</span><span class="innerbox bottombox">Photos by; <a href='https://twitter.com/Poloviiinkin'>@Poloviinkin</a> and <a href='https://www.instagram.com/blpixelartist/'>@blpixelartist</a></span></span>
    </div>
    <div ID="joke" style="display:none"><img src="img/mason.png" width="30px"><span
            style="font-size: 14px;font-weight:lighter">Masons Room</span></div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>        
    <?php include "loginform.php";
    if(isset($_SESSION['admin']))
    if($_SESSION['admin']==true){
        include "adminform.php";}?>
    

    
             <script src="script/fullscreen.js"></script>
             <script src='script/pageExit.js'></script>
</body>
</html>