<?php
session_start();
?>
<!DOCTYPE html>
<html>

<head lang="en">
    <meta charset="utf-8">
    <title>Mason's Tavern Admin Portal</title>
    <?php
    if (!isset($_SESSION['adminVer']) || !isset($_SESSION['logged'])) {
        header('Location: home.php');
    } else if (isset($_SESSION['adminVer']) && isset($_SESSION['logged']))
        if ($_SESSION['adminVer'] != $_SESSION['logged']) {
            header('Location: home.php');
        } else {
            include "../database_mason.php";
            include "analyze.php";?>
        <link rel="stylesheet" href="css/general.css" />
        <link rel="stylesheet" href="css/specific-class.css" />
        <link rel="stylesheet" href="css/specific-id.css" />
        <link rel="stylesheet" href="css/global.css" />
        <link rel="stylesheet" href="css/adminpage.css" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script src="script/jquery-3.1.1.min.js"></script>
    <script src='script/joke.js'></script>
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
</head>

<body>
    <div style="overflow:hidden;">
        <div class="scrolltext"><img src="img/shiba.gif" width="40px" style="transform:scaleX(-1)">Be a friend not a bully!<img src="img/shiba.gif" width="40px" style="transform:scaleX(-1)"></div>
    </div>
    <div id="window" class="outerbox">
        <div id="topbar"><img src="img/mason.png" width="28px">
            <p class="header">Mason's Tavern</p>
            <span class="rowflex-utility-main"><a href="#" class="top-buttons"><img src="img/tasks/minimise.png"></a><a href="#" onclick="fullscreen()" class="top-buttons outerbox" id="screenchange"><img src="img/tasks/fullscreen.png"></a><a href="#" class="top-buttons outerbox" onclick="joke()">
                    <img src="img/tasks/close.png"></a></span>
        </div>

        <?php

            echo ("<nav><a href='home.php'>Home</a>");
            echo ("<a  href='account.php?id=".$_SESSION['logged']."' id='logAccount' name=" . $_SESSION['logged'] . ">Account</a><a  href='#' id='logout' onclick='logout()'>Logout</a></nav>");
            echo ("<input type=hidden id='sessionUSERID' value=" . $_SESSION['logged'] . "></input>");
            echo ("<input type=hidden id='sessionUSERNAME' value=" . $_SESSION['username'] . "></input>");
            echo ("<input type=hidden id='sessionUSERPIC' value=" . $_SESSION['userpic'] . "></input>");
            $userid = $_SESSION['logged'];

        ?>
        </nav>
        <div class="search innerbox">Topic:<span class="search innersearch innerbox">file://<a class="desktop" href="home.php">Home</a>/Admin Tools</span>
        </div>
        <div class='flexbox'>
        <div class='reportbox innerbox'><h2>Reports:</h2>
        <h3>Search for reports: 
        <form class='admin-form' id='reports'> <input id='reports-searchterm'> by: 
                            <select id='reports-type'>
                            <option value='hero_id'>Hero ID</option>
                                <option value='villain_id'>Villain ID</option>
                                <option value='post_id'>Post ID</option>
                                <option value='reply_id'>Reply ID</option>
                            </select>
                            <label for='resolved' style='font-size:0.6em;'>Show Resolved Reports:</label>
                            <input type='checkbox' id='reports-check' name='resolved'>
                            <input type=submit value='Go' class='adminSubmit' name='reports' id='reportsubmit'></h3>
        </form>
            <ul id='reports-list'>
                <?php
                     $query = "SELECT report_id,focus,topic_id,reports.post_id,reports.reply_id,report,heros.username as hero,villains.username as villain,hero_id,villain_id,created_at FROM reports JOIN users as heros ON reports.hero_id=heros.user_id JOIN users as villains ON reports.villain_id=villains.user_id WHERE resolved=false ORDER BY created_at DESC;";
                    $result = $connection->query($query);
                     if (mysqli_num_rows($result) > 0) {
                         foreach ($result as $row) {

                            $id=0;
                            $villain=$link='account.php?id='.$row['villain_id'];
                            $hero=$link='account.php?id='.$row['hero_id'];
                            $link='account.php?id='.$row['villain_id'];
                            if($row['focus']=="post"){
                            $id=$row['post_id'];
                                $link='post.php?post='.$id."&topic=".$row['topic_id'];}
                            if($row['focus']=="reply"){
                                $id=$row['reply_id'];
                                $link='post.php?post='.$row['post_id']."&topic=".$row['topic_id']."#".$id;
                            }if($row['focus']=="account"){
                                $id=$row['villain_id'];
                            }
                            echo "<li id='report-".$row['report_id']."'><span class='rowflex-center'><div class='outerbox reportentry'  id='resolved-0'><a class='hero' href=".$hero.">".$row['hero']."</a> reported <a href=".$villain." class='villain'>".$row['villain']."'s</a> <a href=".$link." class='offense'>".$row['focus']." [".$id."]</a>";
                            echo "</br><div class='innerbox reportbody'>".$row['report']."</br></div>".$row['created_at']."</div>";
                            echo "<span class='colflex-center'><a href='#' onclick='resolve(".$row['report_id'].",1)' class='resolved outerbox'>Resolve</a>";
                            echo "<a href='#' onclick='resolve(".$row['report_id'].",2)' class='resolved outerbox'>Delete</a></span></span></li>";
                         }
                        }
                ?>
            </ul>
    </div>
        <div class="innerbox" id="main2" style="overflow:hidden;">
            <div class='colflex-center'>
                <div class='outerbox admin-block users'>
                <div class="top">Users<span class="rowflex-utility"><a href="#" onclick="fun(this)"
                                class="top-buttons"><img src="img/tasks/minimise.png"></a></span></div>
                                <span>
                    <h3>Search for users:
                        <form class='admin-form' id='users'> <input id='users-searchterm'> by: 
                            <select id='users-type'>
                            <option value='username' selected>Username</option>
                                <option value='email'>Email</option>
                            </select>
                            <label for='banned' style='font-size:0.6em;'>Show Banned Users:</label>
                            <input type='checkbox' id='users-check' name='banned'>
                            <input type=submit value='Go' class='adminSubmit' name='users'>
                        </form>
                    </h3>
                    <div class='innerbox admin-body'  style='display:none;' id='users-body'>
                        <table class='admintable'>
                            <thead><tr>
                                <th>Username</th>
                                <th>User ID</th>
                                <th>Email</th>
                                <th>Posts</th>
                                <th>Replies</th>
                                <th>Reports</th>
                                <th>Ban</th>
                            </tr></thead>
                            <tbody id='users-tablebody'></tbody>
                        </table>
                    </div>
                    </span>
                </div>

                <div class='outerbox admin-block'>
                <div class="top">Posts<span class="rowflex-utility"><a href="#" onclick="fun(this)"
                                class="top-buttons"><img src="img/tasks/minimise.png"></a></span></div>
                                <span >
                    <h3>Search for posts:
                        <form class='admin-form' id='posts'> <input id='posts-searchterm'> by:
                            <select id='posts-type'>
                                <option value='post_title'>Title</option>
                                <option value='content'>Content</option>
                                <option value='username'>Username</option>
                            </select>
                            <label for='deleted' style='font-size:0.6em;'>Show Deleted Posts:</label>
                            <input type='checkbox' id='posts-check' name='deleted'>
                            <input type=submit value='Go' class='adminSubmit' name='posts'>
                        </form>
                    </h3>
                    
                    <div class='innerbox admin-body' style='display:none;' id='posts-body'>
                        <table class='admintable'>
                            <thead>
                            <tr>
                                <th>Username</th>
                                <th>User ID</th>
                                <th>Post ID</th>
                                <th>Title</th>
                                <th>Content</th>
                                <th>Tools</th>
                            </tr></thead>
                            <tbody id='posts-tablebody'></tbody>
                        </table>
                    </div>
                    </span>
                </div>

            </div>
        <?php

        }
        ?>
        </div>
    </div>
    <div class='flexbox'>
    <div class='innerbox reportbox'>
        <h2 style='display:inline;'>Admin Statistics</h2>
        <span class='ratebuttons-enabled outerbox' onclick='loadStat()'>Refresh</span>
            <table id='stats'>
                <?php
                $query="SELECT * FROM stats ORDER BY logDate DESC LIMIT 1";
                $result=$connection->query($query);
                if(mysqli_num_rows($result)>0){
                    $row=$result->fetch_assoc();
            echo "<tr><th>Unique Users Today:</th><td>".$row['uniqueToday']."</td><th>Deleted Posts:</th><td>".$row['deletedPosts']."</td></tr>".
            "<tr><th>This Month:</th><td>".$row['uniqueMonth']."</td><th>Banned Users:</th><td>".$row['bannedUsers']."</td></tr>".
            "<tr><th>Ever:</th><td>".$row['uniqueTotal']."</td><th>Posts Today:</th><td>".$row['postsToday']."</td></tr>".
            "<tr><th colspan='2'>Data Last Refreshed On:</th><td colspan='2'>".$row['logDate']."</td></tr>";}
            else{
                echo "Guess no one has ever hit that button before.... you probably should.";
            }
            ?>
            </table>
    </div>
    <div class='main-split'>
        <div class='tab-row'>
            <span class='tabs tabs-active' name='country-chart'>Users/Country</span>
            <span class='tabs innerbox' name='page-chart'>Avg Page Time</span>
            <span class='tabs innerbox' name='board-chart'>Board Posts</span>
        </div>
        <span class='chart-col' id='country-chart'>
            <span class='chart-label'>Connections by Country</span>
            <div class='charts'><canvas id="countryContainer"></canvas></div>
        </span>
        <span class='chart-col' id='page-chart' style='display:none;'>
            <span class='chart-label'>Average Page Time</span>
            <div class='charts'><canvas id="pageContainer"></canvas></div>
        </span>
        <span class='chart-col' id='board-chart' style='display:none;'>
            <span class='chart-label'>Board Posts</span>
            <div class='charts'><canvas id="boardContainer"></canvas></div>
        </span>
        </div>
    </div>
    <span class='rowflex-center footer-box'><span class="innerbox bottombox">Created by Liam and Jordan</span><span class="innerbox bottombox">Photos by; <a href='https://twitter.com/Poloviiinkin'>@Poloviinkin</a> and <a href='https://www.instagram.com/blpixelartist/'>@blpixelartist</a></span></span>
    </div>
    <div ID="joke" style="display:none"><img src="img/mason.png" width="30px"><span style="font-size: 14px;font-weight:lighter">Masons Room</span></div>

    <?php include "loginform.php";
    include 'chart.php'; ?>

    
    <script src="script/statLoad.js"></script>
    <script src="script/fullscreen.js"></script>
    <script src='script/adminTools.js'></script>
    <script src='script/pageExit.js'></script>
<script src="script/deletehandler.js"></script>
<script src="script/pin.js"></script>
<script>
    $(".tabs").on("click",function(e){
        $(".chart-col").css("display","none");
        $(".tabs-active").addClass("innerbox");
        $(".tabs-active").removeClass("outerbox");
        $(".tabs-active").removeClass("tabs-active");
        $(e.target).addClass("tabs-active");
        $(e.target).addClass("outerbox");
        $(e.target).removeClass("innerbox");
       var hello= $(e.target).attr("name");
       $("#"+hello).css("display","block");

    })
    </script>
</body>

</html>