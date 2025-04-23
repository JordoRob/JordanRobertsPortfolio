<script src=script/adminhandler.js></script>
<div class="login outerbox"id="admin">
        <div class="top"><span>Admin Login</span><span class="rowflex-utility"><a href="#" onclick="adminClose()"
                                    class="top-buttons"><img src="img/tasks/close.png"></a></span></div>
<form>
            <div class="loginForm">
                    <label for="pWord"><b>Admin Password for <?php echo "<img src=".$_SESSION['userpic']." height='30'>".$_SESSION['username']?>:</b></label>
                    <input id='adminPassword' type="password" name="pWord" required>
            
        </br>
                    <input class="loginButton outerbox" id='adminButton' type="submit" value='Login'></input>
                    <span id="adminError" style="display:none;">Password incorrect</span>
        </div>
</form>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
