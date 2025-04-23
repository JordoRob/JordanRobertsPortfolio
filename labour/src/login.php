<?php
include_once("global_generators.php");
if (isset($_GET['status']))
    $status = $_GET['status'];
else
    $status = 0;
?>
<html>

<head>
    <link rel="stylesheet" href="/labour/src/global_styles/styles.css">
    <link rel="stylesheet" href="/labour/src/global_styles/main-content.css">
    <link rel="stylesheet" href="/labour/src/global_styles/navbar.css">
<style>
#login-info{
position:absolute;
bottom:5px;
left:5px;
width:25%;
}
</head>

<body>
    <?php
    echo generate_nav_bar(1, false);
    ?>
    <div class='main-body'>
        <br>
        <h3>Scheduling Board Login</h3>
        <?php
        if ($status == 2) {
            echo "<p class='error'>Succesfully Logged Out</p>";
        }
        ?>
                <form action='processLogin.php' method='post'>
                    <div class='input-wrapper'>
                        <br>
                    <input type='textbox' name='username' id='username' placeholder='Username'><br><br>
                    <input type='password' name='password' id='password' placeholder='Password'><br>
                    <?php
                            if($status==1){ //status=1 is incorrect username or password
                                echo "<label for='password' class='error' id='error'>Username or Password Incorrect</label>";
                            }
                    ?>
</div>
                    <input type='submit' value='Log In' class='button2'>
                </form>
            </div>
<div id='login-info'>
<p> Welcome! This labour scheduling board was done as part of a 4 month capstone project. Working alongside a team of students we
developed this tool to help modernize the scheduling workflow for a local electrical contractor. Many features are disabled in the
portfolio version, however you can feel free to poke around. </p>
</br>
<p> User: portfolioUser </p>
<p> Password: Portfolio123 </p>
</body>

</html>