<?php
session_start();
?><!DOCTYPE html>
<html>

<head lang="en">
    <meta charset="utf-8">
    <title>Forgot Password!</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    
    <script src="script/forgotPasswordHandler.js"></script>
    <?php
    if(isset($_SESSION['logged'])){
        header("Location: home.php");
    }
    ?>
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
            <p class="header">Mason's Room</p>
            <span class="rowflex-utility-main"><a href="#" class="top-buttons"><img src="img/tasks/minimise.png"></a><a
                    href="#" onclick="fullscreen()" class="top-buttons outerbox" id="screenchange"><img src="img/tasks/fullscreen.png"></a><a href="#"
                    class="top-buttons outerbox" onclick="joke()">
                    <img src="img/tasks/close.png"></a></span>
        </div>
    
        <?php
        echo ("<nav><a href='home.php'>Home</a>");
        if(isset($_SESSION['logged'])){
        }else{
            echo("<a href='#' id='logAccount' onclick='loginShow()'>Login</a></nav>");
        }
        
        ?>
        <div class="search innerbox">Topic:<span class="search innersearch innerbox">file://<a class="desktop"
                    href="home.php">Home</a><a class="desktop" href="forgotPassword.php">/Forgot_Password</a></span>
        </div>
        <div class="rectangles innerbox" id="main" style="overflow:hidden;">
            <h2 class="title"><img src="img/espeon.webp" width="50px" style="transform:scaleX(-1)">Forgot Password!~<img src="img/espeon.webp" width="50px"></h2>
                <div class="explorer outerbox" style='width:75%; margin-left:12.5%;'>
                 <div class="top" style="width:98%;"><span>Fill out the form below! </span><span class="rowflex-utility"><a href="#" onclick="fun(this)"
                                class="top-buttons"><img src="img/tasks/minimise.png"></a></span></div>
                    
                    <span class="colflex-left innerbox">
                    
            
                                 
                                    <span class="forgotPassForm">
                                     
                                        <label for="uName"><b>Username:</b></label>
                                        <input id='usernameFP' type="text" name="uName" required> <span id="usernameErrorFP" style="font-size:22px; color:red; display:none;"></br>Username Taken D:</span>
                                            </br>
                                        <label for="email1FP"><b>Email:</b></label>
                                        <input id='email1FP' type="text" name="email1" required>
                                        <label for="email2FP"><b>Confirm Email:</b></label>
                                        <input id='email2FP' type="text" name="email2" required> <span id="emailErrorFP" style="font-size:22px; color:red; display:none;"></br>Emails don't match</span> <span id="emailTaken" style="font-size:22px; color:red; display:none;"></br>Email has been taken</span>
                                            </br>   
                                            <div>Hitting the button below will send you an email containing your current password.</div>                                   
                                        <button id="forgotPasswordButton" style='width:25%;' class='outerbox ratebuttons-enabled' type="submit">Send Password</button><span id="emptyFieldsFP" style="display:none;">Please fill out all required fields!</span><span id="userOrEmailDNEFP" style="display:none;">User and Email either don't exist or are inccorect.</span>
                                    
    </span>
                                



    </span>
    </div>
        </div>
        <span class='rowflex-center footer-box'><span class="innerbox bottombox">Created by Liam and Jordan</span><span class="innerbox bottombox">Photos from;</span></span>
    </div>
    <div ID="joke" style="display:none"><img src="img/mason.png" width="30px"><span
            style="font-size: 14px;font-weight:lighter">Masons Room</span></div>
            
    <?php include "loginform.php"; ?>
    <script>

function fullscreen(){
    var elem = document.fullscreenElement; 
    if(elem==null)
        openFullscreen();
    else{

        exitFullscreen(elem);
}}
        function openFullscreen() {
            var window =document.getElementById("window");
  if (window.requestFullscreen) {
    window.requestFullscreen();
  } else if (window.mozRequestFullScreen) { /* FIREFOX */
    window.mozRequestFullScreen();
  } else if (window.msRequestFullscreen) { /* IE11 */
    window.msRequestFullscreen();
  }
}
function exitFullscreen() {
  if(document.exitFullscreen) {
    document.exitFullscreen();
  } else if (document.mozCancelFullScreen) {
    document.mozCancelFullScreen();
  } else if (document.webkitExitFullscreen) {
    document.webkitExitFullscreen();
  }
}
    </script>
             <script src="script/fullscreen.js"></script>
</body>
</html>