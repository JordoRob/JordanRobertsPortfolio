<?php
$path = './documentation-help-page-related/';
function generateFigureElement($id, $imgFileName, $imgAlt, $imgDataAlt, $caption, $note = null, $noteImportant1 = null, $noteImportant2 = null) {
    global $path;
    $imgSrc = $path . $imgFileName;
    $figure = '<figure id="' . $id . '" class="image-container hovering_element">';
    $figure .= '<img src="' . $imgSrc . '" alt="' . $imgAlt . '" data-alt="' . $path . $imgDataAlt . '">';
    $figure .= '<figcaption><i>' . $caption . '</i></figcaption><img class="expand" onclick=\'show_large_image("' .$imgSrc . '", "'. $path . $imgDataAlt .'")\' src="./Interface/search.svg">';
    if ($note !== null) {
        $figure .= '<div class="optional-div" id="boldText">' . $note . '</div>';
    }
    if ($noteImportant1 !== null) {
        $figure .= '<div class="optional-div" id="important">' . $noteImportant1 . '</div>';
    }
    if ($noteImportant2 !== null) {
        $figure .= '<div class="optional-div" id="important">' . $noteImportant2 . '</div>';
    }
    $figure .= '</figure>';
    echo $figure;
}
?>
<html>
    <head>
        <link rel="stylesheet" href="./global_styles/styles.css">
        <link rel="stylesheet" href="./global_styles/navbar.css">
        <link rel="stylesheet" href="./global_styles/function_pages.css">
        <link rel="stylesheet" href="./global_styles/main-content.css">
        <link rel="stylesheet" href="./global_styles/documentation_help_styles.css">
        <script src="/src/jquery-3.7.0.min.js"></script>        
        <script type="text/javascript" src="/src/admin-view/toTop.js"></script>        
        <script type="text/javascript" src="/src/admin-view/gif-control.js"></script>
        <script src="/src/global_functions.js"></script>
        <script>
            function show_large_image(imgSrc, imgAlt) {
                console.log("imgSrc:", imgSrc);
                console.log("imgAlt:", imgAlt);
                var overlay_id = "picture_large";
                generate_overlay(overlay_id);
                $(`#${overlay_id}`).html(
                    `
                    <div class="gif-container"> 
                    <img src='${imgAlt}' data-alt='${imgSrc}' data-static='${imgSrc}'>
                    </div>
                    `
                );
            }
        </script>
    </head>
    <body>
        <div id="page-container" class="hovering_element"><a id="content"></a>
            <header>
                <h1>Welcome to this Scheduling Board! This user documentation will guide you through the various functions and features. Below are the key sections to make most of your experience. Click <img src="./Interface/search.svg" alt="search" class="expand"> to enlarge gifs/pictures. Click original gifs to start original gifs.</h1>   
                <br> 
                    <ol>
                        <li><a href="#start">Getting Started</a></li>
                        <li><a href="#manager">Managers’ Functions</a></li>
                    </ol>
            </header>
            <br>
            <section>
                <h2><a id="start" href="#content">Getting Started</a></h2>
                <p>Access the Website: use Chrome or Edge for optimal performance</p>
                <p>Create Account: only admin manager can create new account</a></p>
            </section>
            <br>
            <section>
                <h2><a id="manager" href="#content">Managers’ Functions</a></h2>
                <p>The followings are the main features.</p>
                <h3>Job Related Functions</h3>
                <div class="image-container" id="jobRelated">
                <?php
                generateFigureElement('addJob', 'add_job.png', 'add job', 'add_job.gif', 'Add Job');
                generateFigureElement('archiveJob', 'archive_job.png', 'archive job', 'archive_job.gif', 'Archive Job');
                generateFigureElement('unarchiveJob', 'unarchive_job.png', 'unarchive job', 'unarchive_job.gif', 'Unarchive Job');
                generateFigureElement('showAllJobs', 'show_all_jobs.png', 'show all jobs', 'show_all_jobs.gif', 'Show all jobs: archived jobs and job in Jobs Tab listings.');
                generateFigureElement('editJobDetails', 'edit_job.png', 'edit job', 'edit_job.gif', 'Edit Job');
                generateFigureElement('editProjection', 'change_projection.png', 'change projection number', 'change_projection.gif', 'Edit number of employees for job month.', 'Notice: you can enter a number or click/hold the up/down button');
                generateFigureElement('deleteJob', 'delete_job.png', 'delete job', 'delete_job.gif', 'Delete Job');
                ?>
                </div>
                <h3>Employee Related Functions</h3>
                <div class="image-container" id="employeeRelated">
                <?php
                generateFigureElement('addEmployee', 'add_employee.png', 'add employee', 'add_employee.gif', 'Add Employee');
                generateFigureElement('editEmployeeDetails', 'edit_employee_details.png', 'edit employee details', 'edit_employee_details.gif', 'Edit Employee Details');
                generateFigureElement('moveEmployee', 'move_employee.png', 'move employee to a job', 'move_employee.gif', 'Move employee to a job - drag to green zone');
                generateFigureElement('batchAssign', 'batch_assign.png', 'batch assign', 'batch_assign.gif', 'Batch Assign');
                generateFigureElement('moveToArchive', 'move_to_archive_employee.png', 'move employee to archive', 'move_to_archive_employee.gif', 'Move employee to archived: employee with assigned job will show a popup message');
                generateFigureElement('CardToArchive', 'crad_to_archive_employee.png', 'edit employee card to archive', 'crad_to_archive_employee.gif', 'Edit employee card to archived');
                generateFigureElement('moveToInactive', 'move_to_inactive_employee.png', 'move employee to inactive', 'move_to_inactive_employee.gif', 'Move employee to inactive'); 
                generateFigureElement('employeeToMultipleJobs', 'employee_to_multiple_jobs.png', 'one employee, jobs at the same time, show as duplicated', 'employee_to_multiple_jobs.gif', 'Move an employee to multiple jobs at the same time - will show as duplicated');
                generateFigureElement('moveUnassignedEmployeesThroughEmployeeList', 'move_unnasigned_employee_through_employee_list.png', 'move unnasigned employee through employee list', 'move_unnasigned_employee_through_employee_list.gif', 'Move unnasigned employee through employee list');
                generateFigureElement('moveEmployeesThroughWindows', 'move_employees_through_windows.png', 'move employees through windows', 'move_employees_through_windows.gif', 'Move Employees Through Different Windows', 'Note: this is not guaranteed to compatible (just a bonus feature)');
                generateFigureElement('unarchiveEmployee', 'unarchive_employee.png', 'unarchive employee', 'unarchive_employee.gif', 'Unarchive Employee: by dragging or by editing an employee card');
                ?>
                </div>           
                <h3>Order and Search</h3>
                <div class="image-container" id="orderAndSort">
                <?php
                generateFigureElement('customOrder', 'custom_order.png', 'custom order', 'custom_order.gif', 'Custom Order');
                generateFigureElement('orderJob', 'order_job.png', 'order job', 'order_job.gif', 'Order Job');
                generateFigureElement('orderEmployee', 'order_employee.png', 'order employee', 'order_employee.gif', 'Order Employee');
                generateFigureElement('searchJob', 'search_job.png', 'search job', 'search_job.gif', 'Search Job');
                generateFigureElement('searchEmployee', 'search_employee.png', 'search employee', 'search_employee.gif', 'Search Employee: through employee list or Employees Tab');
                ?>
                </div>
                <h3>Projector View</h3>
                <div class="image-container" id="projector">
                <?php
                generateFigureElement('projectorLogin', 'login_as_projector.png', 'login as projector view', 'login_as_projector.gif', 'Login as Projector View User', "Admin can change password" ,"Username: projector", "Password: projector_password");
                generateFigureElement('projectorButton', 'projector_view.png', 'go to projector view', 'projector_view.gif', 'Go to Projector View');
                ?>
                </div>
                <h3>Other Functions</h3>
                <div class="image-container" id="others">
                <?php
                generateFigureElement('changePassword', 'change_password.png', 'change password', 'change_password.gif', 'Change Password');
                generateFigureElement('changeBackground', 'change_background.png', 'change background', 'change_background.gif', 'Change Background');
                ?>
                </div>
            </section>
            <br>     
        </div>
        <!-- <div class="topBtn">
            <button onclick="topFunction()" id="topBtn" title="go to top"><img src="/src/img/toTop.svg" alt="to top">Back to Top</button>
        </div> -->
    </body>
</html>