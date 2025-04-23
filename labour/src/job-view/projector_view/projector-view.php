<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
if (!isset($_SESSION['projector'])) {
    security_check_and_connect();    
}
?>

<html>

<style>
    body {
        overflow-x: hidden;
    }

    /* 
    Options below for setting default zoom. 
    First only works on webkit browsers. 
    Second works on all browsers.
    Both limits width of projection numbers.
    */

    /* html {
        zoom: 50%;
    } */

    /* html {
        transform: scale(50%);
        transform-origin: top left;
        width: 200%;
        height: 200%;
    } */
</style>

<head>
    <link rel="stylesheet" href="/labour/src/global_styles/styles.css">
    <link rel="stylesheet" href="/labour/src/global_styles/main-content.css">
    <link rel="stylesheet" href="/labour/src/Employee Page/employee_styles.css">
    <link rel="stylesheet" href="../job_styles.css">
    <link rel="stylesheet" href="projector_view.css">

    <script src="/labour/src/jquery-3.7.0.min.js"></script>
    <script src="/labour/src/global_functions.js"></script>
    <script src="projector_view.js"></script>
</head>

<body>
    <div id='job-listings'></div>
</body>

<footer>
    <div id='inactive-employees' title='Click to toggle Inactive/In School Employees'>
        <span id='inactive_wrapper' class='fill_width'>            
            <div class='inactive_title'>Inactive</div>
            <div id='inactive-listings' class='employee_wrapper active_status_wrappers'></div>
        </span>
        <span id='in_school_wrapper' class='fill_width'>
            <div class='inactive_title'>In School</div>
            <div id='in-school-listings' class='employee_wrapper active_status_wrappers'></div>
        </span>
    </div>
    <div class='totals-wrapper'></div>
</footer>

<div id="message_container" style="display: none;"></div>

<script>
    $(document).ready(function() {
        projector_search();
        projector_totals();
        inactive_update()
        projector_refresh();
    });
</script>
</html>