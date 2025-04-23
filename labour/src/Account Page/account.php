<?php
// Login Check
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
security_check_and_connect();
?>
<html>

<head>
    <link rel="stylesheet" href="/labour/src/global_styles/styles.css">
    <link rel="stylesheet" href="/labour/src/global_styles/main-content.css">
    <link rel="stylesheet" href="/labour/src/global_styles/navbar.css">
    <link rel="stylesheet" href="/labour/src/global_styles/function_pages.css">
    <link rel="stylesheet" href="/labour/src/Account Page/account.css">
    <script src="/labour/src/jquery-3.7.0.min.js"></script>
</head>

<body>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/labour/src/global_generators.php");
    echo generate_nav_bar(3, true);
    ?>
    <br>
    <div id="main_content">
        <!-- CHANGE USERNAME -->
        <form id="change_username" class="hovering_element">
            <div class="form_message"></div>
            <fieldset>
                <legend>Change Username</legend>
                <div id="current_username" class="form_field hovering_element"><?php
                    // Get username
                    $stmt = $pdo->prepare("SELECT username FROM user WHERE id = ?");
                    $stmt->execute([$_SESSION['user']]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo "Current Username: <i>" . $row['username'] . "</i>";
                ?></div>
                <div class="form_field hovering_element">
                    <label for="new_username">New Username: </label>
                    <input type="text" name="new_username">
                </div>                
                <div class="form_buttons">
                    <button type="submit">Change Username</button>
                    <script>
                        $("#change_username").submit(function(e) {
                            e.preventDefault();
                            $("#change_username .form_message").html("This is disabled");                      
});
                    </script>
                </div>
            </fieldset>
        </form>

        <!-- CHANGE PASSWORD -->
        <form id="change_password" class="hovering_element">
            <div class="form_message"></div>
            <fieldset>
                <legend>Change Password</legend>
                <div class="form_field hovering_element">
                    <label for="old_pass">Old Password: </label>
                    <input type="password" name="old_pass" required>
                </div>
                <div class="form_field hovering_element">
                    <label for="new_pass">New Password: </label>
                    <input type="password" name="new_pass" required>
                </div>
                <div class="form_field hovering_element">
                    <label for="confirm_pass">Confirm Password: </label>
                    <input type="password" name="confirm_pass" required>
                </div>
                <div class="form_buttons">
                    <button type="submit">Change Password</button>
                    <script>
                        $("#change_password").submit(function(e) {
                            e.preventDefault();
                            $("#change_password .form_message").html("This is disabled");                        
});
                    </script>
                </div>
            </fieldset>
        </form>
        <!-- CHANGE BACKGROUND -->
        <form id="change_background" class="hovering_element" enctype="multipart/form-data">
            <div class="form_message"></div>
            <fieldset>
                <legend>Change Background</legend>
                <div class="form_field hovering_element">
                    <label>Current Background: </label>
                    <?php 
                        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/labour/src/img/backgrounds/" . $_SESSION['user'] . ".jpg")) {
                            echo "<img id='preview_background' src='/labour/src/img/backgrounds/" . $_SESSION['user'] . ".jpg'>";
                        } else {
                            echo "<img id='preview_background'><p id='no_background'>None</p>";
                        }
                    ?>
                </div>
                <div class="form_field hovering_element">
                    <label for="background_upload"></label>
                    <input type="file" name="background_upload" accept='image/*' required>
                </div>
                <div class='function_note'>Sometimes your browser may retain a cached version of the image after change. Press Ctrl + F5 to force a refresh.</div>
                <div class="form_buttons">
                    <button data-enabled=<?php echo $_SESSION['use_background'];?> id='background_toggle' type="button"
                        <?php echo file_exists($_SERVER['DOCUMENT_ROOT'] . "/labour/src/img/backgrounds/" . $_SESSION['user'] . ".jpg") ? "" : "style='display: none;'";?>
                    >
                        <?php 
                            if ($_SESSION['use_background']) {
                                echo "Currently Enabled";
                            } else {
                                echo "Currently Disabled";
                            }
                        ?>
                    </button>
                    <button type="submit">Upload Selected</button>
                </div>
            </fieldset>
        </form>

        <!-- CHANGE REFRESH TIMER -->
        <form id="set_interval" class="hovering_element">
    <div class="form_message"></div>
    <fieldset>
        <legend>Set Refresh Interval</legend>
        <div class="form_field hovering_element">
            <label for="refreshRange">Select Auto-Refresh Interval</label>
            <input type="range" id="refreshRange" name="refreshRange" min="5" max="300">
            <label for="refreshRange"><span id='refreshSeconds'></span> Seconds <span id='refreshMinutes'></span></label>
            <label for="disableRange">Disable Auto-Refresh</label>
            <input type="checkbox" id="disableRange" name="disableRange">
        </div>
        <div class="form_buttons">
            <button type="submit">Set Refresh Interval</button>
            <script>
                console.log( $("#disableRange").is(":checked"));
                $("#set_interval").submit(function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: "set_refresh_interval.php",
                        type: "POST",
                        data: {
                            disabled: $("#disableRange").is(":checked"),
                            timerVal: $("#refreshRange").val()
                        },
                        success: function(data) {
                            var response=JSON.parse(data);
                            $("#set_interval .form_message").html(response[0]);
                            if(response[1]<=300)
                            localStorage.setItem('timer',response[1]);
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            $("#set_interval .form_message").html("Error: " + jqXHR.responseText);
                        }
                    });
                });
            </script>
        </div>
    </fieldset>
</form>

        <script>
let saved=localStorage.getItem('timer');
if(saved==0){
    $("#disableRange").prop("checked",true);
    $("#refreshRange").prop( "disabled", true );
}
$("#refreshRange").val(saved);
$("#refreshSeconds").html(saved);
if(saved>60){
let minuteString=Math.round(saved/60)+" Minutes "+saved%60+" Seconds";
    $("#refreshMinutes").html("("+minuteString+")"); 
}

$("#refreshRange").on("input",function(){
    let selected=$("#refreshRange").val()
    $("#refreshSeconds").html(selected);
    if(selected>60){
    let minuteString=Math.round(selected/60)+" Minutes "+selected%60+" Seconds";
    $("#refreshMinutes").html("("+minuteString+")"); 
}else{
    $("#refreshMinutes").html("");
}
});

$("#disableRange").on("click",function(){
    if($("#disableRange").is(":checked")){
        $("#refreshRange").prop( "disabled", true );
    }else{
        $("#refreshRange").prop( "disabled", false );
    }
})
        </script>
        <script>
            $('#background_toggle').hover(function() {
                if (this.dataset.enabled == 1) {
                    this.textContent = 'Click to Disable';
                } else {
                    this.textContent = 'Click to Enable';
                }
            }, function() {
                if (this.dataset.enabled == 1) {
                    this.textContent = 'Currently Enabled';
                } else {
                    this.textContent = 'Currently Disabled';
                }
            });

            function enable_background() {
                var timestamp = new Date().getTime();
                $('#background_image_styles').html(`
                    body {
                        background-image: url('/labour/src/img/backgrounds/<?php echo $_SESSION['user']; ?>.jpg?${timestamp}');
                        background-size: cover;
                        background-repeat: no-repeat;
                        background-attachment: fixed;
                        background-position: center;
                    }
                `);
            }

            function toggle_background () {
                var toggleButton = $(this);
                $.ajax({
                    url: "toggle_background.php",
                    type: "POST",
                    data: {
                        toggle: toggleButton.attr('data-enabled') == 1 ? 0 : 1
                    },
                    success: function(response) {
                        toggleButton.attr('data-enabled', toggleButton.attr('data-enabled') == 1 ? 0 : 1);
                        
                        console.log((response))
                        if (response == 1) {
                            $('#background_toggle').text('Background Enabled');
                            enable_background();
                        } else {
                            $('#background_toggle').text('Background Disabled');
                            $('#background_image_styles').html('');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert("Error: " + jqXHR.responseText);
                    }
                });

            }

            $('#background_toggle').click(toggle_background);

            $("#change_background").submit(function(e) {
                e.preventDefault(); // Prevent form submission and page reload
                $("#change_background .form_message").html("This is disabled");

            });
        </script>
    </div>
<body>

<?php 
    if ($_GET['password_change']) {
        echo "
            <script>
                alert('Your password has recently been reset. You must change your password before continuing.');
            </script>
        ";
    }
?>