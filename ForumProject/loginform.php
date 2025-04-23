<script src="script/loginhandler.js"></script>
<div class="login outerbox" id="login">
        <div class="top"><span>Login! </span><span class="rowflex-utility"><a href="#" onclick="loginClose()"
                                    class="top-buttons"><img src="img/tasks/close.png"></a></span></div>

            <div  class="loginForm">
                <form>
                    <label for="uName"><b>Username:</b></label>
                    <input id='username' type="text" name="uName" maxlength="16" required>
        </br>
                    <label for="pWord"><b>Password:</b></label>
                    <input id='password' type="password" name="pWord" maxlength="20"required>
            
        </br>
                    <input class="loginButton outerbox" id='loginBut' type="submit" value='Login'></input>
                    <button class="loginButton outerbox"  onclick="window.location.href='register.php'">Sign-Up</button>
                    <button style="width: 43%;;" class="loginButton outerbox"  onclick="window.location.href='forgotPassword.php'">Forgot Password</button>
                    <span id="loginError" style="display:none;">Username or password incorrect</span>
</form>
        </div>
    </div>
