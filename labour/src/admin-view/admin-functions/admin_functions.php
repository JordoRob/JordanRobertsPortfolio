<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
security_check_and_connect();
check_is_admin();

include_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/global_generators.php";
?>
<html>
    <head>
        <link rel="stylesheet" href="/labour/src/global_styles/styles.css">
        <link rel="stylesheet" href="/labour/src/global_styles/navbar.css">
        <link rel="stylesheet" href="/labour/src/global_styles/function_pages.css">
        <link rel="stylesheet" href="/labour/src/global_styles/overlay_styles.css">
        <link rel="stylesheet" href="/labour/src/Employee Page/Pin Bar Functions/pin-bar-functions.css">
        <link rel="stylesheet" href="/labour/src/admin-view/admin_styles.css">
        <script src="/labour/src/jquery-3.7.0.min.js"></script>
        <script src="/labour/src/global_functions.js"></script>
    </head>
    <body>
        <?php
            echo generate_nav_bar(1, 2);
        ?>
        <div id="message_container" style="display: none;"></div>
        <br>
        <script>
            const no_ajax = true; // Hack solution for not calling ajax_search when overlays close (see global_functions.js)
            function open_temp_pass_overlay(temp_pass, action) {
                var overlay_id = "temp_pass";
                generate_overlay(overlay_id, "40em", "5em");
                $("#" + overlay_id).html(
                    `
                    <h1 class="info-name">Notice</h1>
                    <span id="overlay_separator_line"></span>
                    <p class="overlay_notification_message">Account ${action} successfully. The temporary password generated is:</p>
                    <input readonly type="text" value="` + temp_pass + `">
                    <div class="overlay_notification_buttons">
                        <div class="overlay_button reject_hover" onclick="closeOverlay('${overlay_id}')">Close</div>
                        <div class="overlay_button confirm_hover" onclick="copyToClipboard('${temp_pass}')">Copy to Clipboard</div>
                    </div>
                    `
                )
            }
        </script>

        <div id="main_content">

            <!-- CREATE USER ACCOUNT -->
            <form id="create_account" class="hovering_element">
                <div class="form_message"></div>
                <fieldset>
                    <legend>Add User Account</legend>
                    <div class="form_field hovering_element">
                        <label for="username">Username: </label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="form_buttons">
                        <button type="submit">Add Account</button>
                        <script>
                            $("#create_account").submit(function(e) {
                                e.preventDefault();
                                $.ajax({
                                    url: "create_manager.php",
                                    type: "POST",
                                    data: {
                                        username: $("input[name='username']").val()
                                    },
                                    success: function(data) {
                                        var response = JSON.parse(data);
                                        open_temp_pass_overlay(response.temp_pass, "created");
                                        $('#select_manager_usernames').append('<option value="' + response.id + '">' + response.username + '</option>');
                                    },
                                    error: function(jqXHR, textStatus, errorThrown) {
                                        $("#create_account .form_message").html("Error: " + jqXHR.responseText);
                                    }
                                });
                            });
                        </script>
                    </div>
                    <div class='function_note'>
                        After creation, managers will be prompted to change their password when they login
                    </div>
                </fieldset>
            </form>

            <!-- RESET USER PASSWORD -->
            <form id="reset_password" class="hovering_element">
                <div class="form_message"></div>
                <fieldset>
                    <legend>Reset User Password</legend>
                    <div class="form_field hovering_element">
                        <label for="username">Username: </label>
                        <select id="select_manager_usernames">
                            <?php
                                $users = $pdo->query("SELECT username, id FROM user WHERE is_admin = 0");
                                foreach ($users as $user) {
                                    echo "<option value='" . $user['id'] . "'>" . $user['username'] . "</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form_buttons">
                        <button type="submit">Reset Password</button>
                        <script>
                            $("#reset_password").submit(function(e) {
                                e.preventDefault();
                                generate_overlay("reset_password_confirm", "40em", "5em");
                                $("#reset_password_confirm").html(
                                    `
                                    <h1 class="info-name">Notice</h1>
                                    <span id="overlay_separator_line"></span>
                                    <p class="overlay_notification_message">Are you sure you want to reset the password for this account?</p>
                                    <div class="overlay_notification_buttons">
                                    <div class="overlay_button reject_hover" onclick="closeOverlay('reset_password_confirm')">Cancel</div>
                                        <div id="reset_confirm" class="overlay_button confirm_hover">Reset Password</div>
                                    </div>
                                    `
                                )
                                $("#reset_confirm").click(function() {
                                    closeOverlay('reset_password_confirm')
                                    $.ajax({
                                        url: "reset_password.php",
                                        type: "POST",
                                        data: {
                                            id: $("#select_manager_usernames").val()
                                        },
                                        success: function(data) {
                                            var response = JSON.parse(data);
                                            open_temp_pass_overlay(response.temp_pass, "password reset");
                                        },
                                        error: function(jqXHR, textStatus, errorThrown) {
                                            $("#reset_password .form_message").html("Error: " + jqXHR.responseText);
                                        }
                                    });
                                });                                
                            });

                        </script>
                    </div>
                    <div class='function_note'>
                        After reset, managers will be prompted to change their password when they next login
                    </div>
                </fieldset>
            </form>
            
            <!-- CHANGE ADMIN PASSWORD -->
            <form id="change_admin_password" class="hovering_element">
                <div class="form_message"></div>
                <fieldset>
                    <legend>Change Admin Password</legend>
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
                            $("#change_admin_password").submit(function(e) {
                                e.preventDefault();
                                $.ajax({
                                    url: "change_password.php",
                                    type: "POST",
                                    data: {
                                        old_pass: $("input[name='old_pass']").val(),
                                        new_pass: $("input[name='new_pass']").val(),
                                        confirm_pass: $("input[name='confirm_pass']").val()
                                    },
                                    success: function(data) {
                                        $("#change_admin_password .form_message").html(data);                                        
                                        $("#change_admin_password input").val("");
                                    },
                                    error: function(jqXHR, textStatus, errorThrown) {
                                        $("#change_admin_password .form_message").html("Error: " + jqXHR.responseText);
                                    }
                                });
                            });
                        </script>
                    </div>
                </fieldset>
            </form>

            <!-- CHANGE PROJECTOR PASSWORD -->
            <form id="change_projector_password" class="hovering_element">
                <div class="form_message"></div>
                <fieldset>
                    <legend>Change Projector Password</legend>
                    <div class="form_field hovering_element">
                        <label for="new_projector_pass">New Password: </label>
                        <input type="password" name="new_projector_pass" required>
                    </div>
                    <div class="form_buttons">
                        <button type="submit">Change Password</button>
                        <script>
                            $("#change_projector_password").submit(function(e) {
                                e.preventDefault();
                                $.ajax({
                                    url: "change_projector_password.php",
                                    type: "POST",
                                    data: {
                                        new_pass: $("input[name='new_projector_pass']").val()
                                    },
                                    success: function(data) {
                                        $("#change_projector_password .form_message").html(data);                                        
                                        $("#change_projector_password input").val("");
                                    },
                                    error: function(jqXHR, textStatus, errorThrown) {
                                        $("#change_projector_password .form_message").html("Error: " + jqXHR.responseText);
                                    }
                                });
                            });
                        </script>
                    </div>
                    <div class='function_note'>
                        To login as projector, use the username "projector" and the password you set here
                    </div>
                </fieldset>
            </form>
            
            <!-- LOAD DATABASE BACKUP -->
            <form id="load-backup" class="hovering_element" action = 'loadBackup.php' method = 'post'>
                <fieldset>
                    <legend>Load Database backup</legend>

                    <div class="form_field hovering_element">
                        <label for="username">File: </label>
                        <select id="select_database_backups" name="file">
                            <?php
                            $backupDir = '../../../backups/';
                            $backupFiles = glob($backupDir . '*.sql');
                            foreach ($backupFiles as $file) {
                                $fileName = basename($file);
                                echo '<option value="' . $fileName . '">' . $fileName . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form_buttons">
                        <button type="submit" onclick="return confirm('Warning: Loading a backup will overwrite the current databases. Do you want to proceed?')">Load Backup File</button>
                    </div>
                </fieldset>
            </form>

            <form id="download-backup" class="hovering_element" method = 'post' action = 'createBackupFile.php'>
                <fieldset>
                    <legend>Download Database Backup</legend>
                    <div class="form_buttons">
                        <button type="submit" name="backup">Backup Database</button>
                    </div>
                </fieldset>
            </form>

            <form id="upload-backup" class="hovering_element" action="uploadBackup.php" method="post" enctype="multipart/form-data">
                <fieldset>
                    <legend>Upload Database backup</legend>

                    <div class="form_field hovering_element">
                        <label for="backupFile"></label>
                        <input type="file" id="backupFile" name="backupFile" accept=".sql" required>
                    </div>
                    <div class="form_buttons">
                        <button type="submit" onclick="return confirm('Warning: Loading a backup will overwrite the current databases. Do you want to proceed?')">Upload Backup File</button>
                    </div>
                </fieldset>
            </form>

            <form id="upload-job-csv" class="hovering_element" method="post" enctype="multipart/form-data" action="processUploadJobsCSV.php">
                <fieldset>
                    <legend>Upload Job CSV</legend>

                    <div class="form_field hovering_element">
                    <label for="csvFile"></label>
                    <input type="file" name="jobsCSVFile" id="jobsCSVFile" accept=".csv" required>
                    <br>
                    </div>
                    <div class="form_buttons">
                    <button type="submit" value="Upload Job csv"> Upload Job csv</button>
                    </div>
                </fieldset>
            </form>

            <form id="upload-employee-csv" class="hovering_element" method="post" enctype="multipart/form-data" action="processUploadEmployeeCSV.php">
                <fieldset>
                    <legend>Upload Employee CSV</legend>

                    <div class="form_field hovering_element">
                    <label for="csvFile"></label>
                    <input type="file" name="employeeCSVFile" id="employeeCSVFile" accept=".csv" required>
                    <br>
                    </div>
                    <div class="form_buttons">
                    <button type="submit" value="Upload employee csv"> Upload employee csv</button>
                    </div>
                </fieldset>
            </form>
        </div>
    </body>
</html>