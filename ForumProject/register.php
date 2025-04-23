<?php
session_start();
?><!DOCTYPE html>
<html>

<head lang="en">
    <meta charset="utf-8">
    <title>Mason's Tavern</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="script/registerhandler.js"></script>
    <script src='script/joke.js'></script>
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
        echo ("<nav><a href='home.php'>Home</a>");

        if(isset($_SESSION['logged'])){
            
        }else{
            echo("<a href='#' id='logAccount' onclick='loginShow()'>Login</a></nav>");
        }
        
        ?>
        <div class="search innerbox">Topic:<span class="search innersearch innerbox">file://<a class="desktop"
                    href="home.php">Home</a><a class="desktop" href="register.php">/Register</a></span>
        </div>
        <div class="rectangles innerbox" id="main" style="overflow:hidden;">
            <h2 class="title"><img src="img/espeon.webp" width="50px" style="transform:scaleX(-1)">Register!~<img src="img/espeon.webp" width="50px"></h2>
            <div class='rowflex-center'>
                <span class="colflex-center outerbox">
                 <div class="top" style="width: 99%;"><span>Fill out the form below! </span><span class="rowflex-utility"><a href="#" onclick="fun(this)"
                                class="top-buttons"><img src="img/tasks/minimise.png"></a></span></div>
                    
                    <div class="rectangles colflex-left innerbox">
                    
            
                                 
                                    <div class="registerForm">
                                     
                                        <label for="uName"><b>Username:</b></label>
                                        <input id='username1' type="text" name="uName" maxlength="16" required> <span id="usernameError" style="font-size:22px; color:red; display:none;"></br>Username Taken D:</span>
                                            </br>
                                        <label for="email1"><b>Email:</b></label>
                                        <input id='email1' type="text" name="email1" required>
                                        <label for="email2"><b>Confirm Email:</b></label>
                                        <input id='email2' type="text" name="email2" required> <span id="emailError" style="font-size:22px; color:red; display:none;"></br>Emails either don't match or are invalid</span> <span id="emailTaken" style="font-size:22px; color:red; display:none;"></br>Email has been taken</span>
                                            </br>
                                        <label for="password1" ><b>Password:</b></label>
                                        <input  id='password1' type="password" name="pWord1" maxlength="20" required>
                                        <label for="password2"><b>Confirm Password:</b></label>
                                        <input id='password2' type="password" name="pWord2" maxlength="20" required> <span id="passwordError" style="display:none;"></br> Passwords don't match</span><span id="passwordStrength" style="display:none;"></br> Password must be at least eight characters and include one capital, one number and one special character. </span>
                                            </br>
                                        <span> Choose a profile pic! Or visit your account page to upload your own.</span>
                                            </br>
                                        <img id="1"  onclick="addPic(this.getAttribute('src'), this.id);" class="profileImgSelect innerbox" src="img\user\archer.png" alt="SkeletonWithBow">
                                        <img id="2"  onclick="addPic(this.getAttribute('src'), this.id);" class="profileImgSelect" src="img\user\sword.png" alt="SkeletonWithSword">
                                        <img id="3"  onclick="addPic(this.getAttribute('src'), this.id);" class="profileImgSelect" src="img\user\mage.png" alt="SkeletonWithMagic">
                                        <img id="4"  onclick="addPic(this.getAttribute('src'), this.id);" class="profileImgSelect" src="img\user\spear.png" alt="SkeletonWithSpear">
                                        <img id="5"  onclick="addPic(this.getAttribute('src'), this.id);" class="profileImgSelect" src="img\user\vanguard.png" alt="SkeletonWithGuardStatus">
                                            </br>
                                            
                                        <button class="registerButton outerbox" type="submit">Register!</button><span id="emptyFields" style="display:none;">Please fill out all required fields!</span>
                                    
                                    </div>
                                



                    </div>
                </span>
            </div>
        </div>
        <span class='rowflex-center footer-box'><span class="innerbox bottombox">Created by Liam and Jordan</span><span class="innerbox bottombox">Photos by; <a href='https://twitter.com/Poloviiinkin'>@Poloviinkin</a> and <a href='https://www.instagram.com/blpixelartist/'>@blpixelartist</a></span></span>
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