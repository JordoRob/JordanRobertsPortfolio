<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
security_check_and_connect();
?>

<img src='/labour/src/img/close.svg' class='close_overlay' onclick='hideEmpOverlay(true)' />
<div class='info-wrapper'>
    <form id='add_employee' enctype='multipart/form-data'>
        <div class='overlay_head'>
            <div class='overlay_head_text'>
                <span class='info-name'><input type='text' name='name' placeholder='Name' required />
            </span>
                <span class='info-line'></span>
                <span id='info-title' class='info-subheading'>
                    <span id='overlay_name_error' class='overlay_error overlay_required_error'></span>
                    <select name='title' id='role-select' required>
                        <option value='' disabled>Select Role</option>
                        <?php
                        include $_SERVER['DOCUMENT_ROOT'] . "/labour/src/global_data_table.php";
                        for ($i = 0; $i < count($display_role); $i++) {
                            echo "<option value='$i'>$display_role[$i]</option>";
                        }
                        ?>
                    </select>
                </span>
            </div>
            <div class='overlay_profile_pic_wrapper'>
                <img class='overlay_profile_pic' id='add-employee-profile-pic' src='../img/emp/default.svg' />
                <input name='upload' type='file' id='profile_upload' accept='image/*' onchange='addEmpImg()' hidden />
                <label for='profile_upload' title='Upload Picture' id='file_upload_label'>
                    <span>Click to Upload New Image</span>
                </label>
            </div>
        </div>
        <div class='overlay_info_box_wrapper'>
            <div class='overlay_double_wrapper'>
                <div class='overlay_info_box overlay_phone'>
                    <span>Phone Number</span>
                    <input type='text' name='phone' id='phone_datafield' value='' />
                    <span id='overlay_phone_error' class='overlay_error'>Placeholder</span>
                </div>
                <div class='overlay_info_box overlay_phone'>
                    <span>Secondary</span>
                    <input type='text' name='phoneSec' id='phone_datafield_secondary' value='' />
                    <span id='overlay_phoneSec_error' class='overlay_error'>Placeholder</span>
                </div>
            </div>
            <div class='overlay_info_box'>
                <span>Email</span>
                <input type='text' name='email' id='email_datafield' value='' />
                <span id='overlay_email_error' class='overlay_error'>Placeholder</span>
            </div>
            <div class='overlay_info_box'>
                <span>Date Hired</span>
                <input type='date' name='datehired' id='datehired_datafield' value=''>
                <span id='overlay_datehired_error' class='overlay_error'>Placeholder</span>
            </div>
            <div class='overlay_info_box'>
                <span>Birthday</span>
                <input type='date' name='birthday' id='birthday_datafield' value=''>
                <span id='overlay_birthday_error' class='overlay_error'>Placeholder</span>
            </div>
        </div>
        <div class='overlay_notes'>
            <span>Notes:</span>
            <textarea id='notes_datafield' name='notes' value=''></textarea>
        </div>
        <button type='submit' class='add_emp_button' id='add_emp_button'>Add Employee</button>
        <button type='reset' class='add_emp_button' id='add_emp_button_reset'>Reset Fields</button>
    </form>
</div>