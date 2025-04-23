<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
security_check_and_connect();
check_is_admin();
?>
<?php
$path = '/labour/src/img/documentation-help-page-related/';
function generateFigureElement($id, $imgFileName, $imgAlt, $imgDataAlt, $caption, $note = null, $noteImportant1 = null, $noteImportant2 = null) {
    global $path;
    $imgSrc = $path . $imgFileName;
    $figure = '<figure id="' . $id . '" class="image-container hovering_element">';
    $figure .= '<img src="' . $imgSrc . '" alt="' . $imgAlt . '" data-alt="' . $path . $imgDataAlt . '">';
    $figure .= '<figcaption><i>' . $caption . '</i></figcaption><img class="expand" onclick=\'show_large_image("' .$imgSrc . '", "'. $path . $imgDataAlt .'")\' src="/labour/src/img/Interface/search.svg">';
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
include_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/global_generators.php";
?>
<html>
    <head>
        <link rel="stylesheet" href="/labour/src/global_styles/styles.css">
        <link rel="stylesheet" href="/labour/src/global_styles/navbar.css">
        <link rel="stylesheet" href="/labour/src/global_styles/function_pages.css">
        <link rel="stylesheet" href="/labour/src/admin-view/admin_styles.css">
        <link rel="stylesheet" href="/labour/src/global_styles/main-content.css">
        <link rel="stylesheet" href="/labour/src/global_styles/documentation_help_styles.css">
        <script src="/labour/src/jquery-3.7.0.min.js"></script>        
        <script type="text/javascript" src="/labour/src/admin-view/toTop.js"></script>        
        <script type="text/javascript" src="/labour/src/admin-view/gif-control.js"></script>
        <script src="/labour/src/global_functions.js"></script>
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
        <?php
            echo generate_nav_bar(0, 2);
        ?>
        <div id="page-container" class="hovering_element"><a id="content"></a>
            <header>
                <h1>Welcome to this Scheduling Board! This user documentation will guide you through the various functions and features. Below are the key sections to make most of your experience. Click <img src="/labour/src/img/Interface/search.svg" alt="search" class="expand"> to enlarge gifs/pictures. Click original gifs to start original gifs.</h1>   
                <br> 
                    <ol>
                        <li><a href="#features">Scheduler Features</a> (You may want to check it out)</li>
                        <li><a href="#troubleshooting">Troubleshooting</a> (You may want to check it out)</li>
                        <li><a href="#admin">Admin’s Functions</a></li>
                        <li><a href="#contact">Contact and Support</a></li>
                        <li><a href="#start">Getting Started</a></li>
                        <li><a href="#manager">Managers’ Functions</a></li>
                    </ol>
            </header>
            <br>
            <section>
            <h2><a id="features" href="#content">Scheduler Features</a></h2>
                <p><a id="final_report"></a> Final report of this website is available <a href="/docs/final/Final Documentation.pdf" download="UBCO Capstone Summer 2023 - Final Documentation.pdf" >here</a>.</p>
                <p>Communication log between the developing team and the Horizon Electric managers is available <a href="/docs/communication/UBCO Capstone Summer 2023 - Communication Log.pdf" download>here</a>. The "to client" folder mentioned in the log is available <a href="/docs/communication/to client.zip" download="UBCO Capstone Summer 2023 - to client.zip" >here</a>. The "from client" folder mentioned in the log is available <a href="/docs/communication/from client.zip" download="UBCO Capstone Summer 2023 - from client.zip">here</a>.</p>
                <p>The scheduler saves any modifications made during the day at midnight.</p>
                    <p>The scheduler runs under a Docker container. What is Docker and its container: Docker allows you to create and run containers. Containers are like small packages that contain everything need to run an application, such as the code, the libraries, the settings, and so on. Containers are useful because they make it easy to move application from one computer to another without worrying about compatibility issues or installation problems. Here is a <a href="https://youtu.be/Gjnup-PuquQ">video</a> explains Docker.</p>
                    <p>Database is available at: http://localhost:5000 (replace localhost with your static ip), then click "HE_Schedule" in the right.</p>
                    <?php
                    generateFigureElement('databaseInFeatures', 'database.png', 'database', 'database.gif', 'Database');
                    ?>
                    <p>The scheduler uses port 5000 for phpmyadmin(phpmyadmin is a software tool to manage a database) and port 8080 for the website. If any conflicts arise, these ports can be changed. In all demonstration, the develper used different port numbers than what deployed in the office.</p>
                    <p>The website developed using HTML, CSS, JS, PHP, and MySQL as the primary programming languages.</p>
                    <p>The developers validate user login with frontend and backend validation and encrypt passwords via hash before storing them in a database.</p>
                    <p>The website is hosted on a local server that does not have access to the internet, therefore no web-based APIs on the server may be used.</p>
                        <p>Use case flowchart below. It defines particioants(manager and admin in this case), as well as their roles and interactions.</p>
                        <?php
                        generateFigureElement('useCase', 'UseCaseDiagram.png', 'use case flowchart', 'UseCaseDiagram.png', 'Use Case Flowchart');
                        ?>
                        <p>Sequence diagram below. Sequence diagrams are all similar, so this is an example of editing projection/outlook number. More sequence diagrams are available in <a href="#final_report">final report</a>.</p>
                        <?php
                        generateFigureElement('sequence', 'editOutlook.png', 'sequence diagram', 'editOutlook.png', 'Sequence diagrams of editing projection/outlook number');
                        ?>
                        <p>Architecture diagram (with deployment) below. This is a high level overview of the main components of the system and how they connect.</p>
                        <?php
                        generateFigureElement('architectureDiagram', 'architectureDiagram.png', 'architecture diagram', 'architectureDiagram.png', 'Architecture Diagram with Deployment');
                        ?>
                        <p>Database design is as follow. This is a high level diagram that describes how information flows between different components of the system.</p>
                        <div class="image-container" id="databaseDesign">
                        <?php
                        generateFigureElement('erDiagram', 'ERDiagram.png', 'ER diagram', 'ERDiagram.png', 'ER Diagram');
                        generateFigureElement('DFD_Level0', 'DFD_Level0.png', 'DFD Level0', 'DFD_Level0.png', 'Data Flow Diagram Level 0');
                        generateFigureElement('DFD_Level1', 'DFD_Level1.png', 'DFD Level1', 'DFD_Level1.png', 'Data Flow Diagram Level 1');
                        ?>
                        </div>
                           
            </section>
            <br>
            <section>
                <h2><a id="troubleshooting" href="#content">Troubleshooting</a></h2>
                <p>If you encounter any issues, first, try to restart the computer. If that doesn't work, you can try resetting the Docker containers using the following steps:</p>
                <p>In terminal, navigate to the project folder (here is a <a href="https://www.wikihow.com/Open-a-Folder-in-Cmd#:~:text=The%20%22cd%22%20command%20is%20used,not%20press%20Enter%20just%20yet.&text=Type%20the%20address%20of%20the,the%20folder%20is%20located%20in.">wikihow link</a> instruction).</p>
                <p>Type “docker compose down &nbsp -v” to stop Docker container. <span id="important">Caution! THIS WILL RESET THE DATABASE!</span> Make sure to download a backup first!</p>
                <p>Type “docker compose up &nbsp -d &nbsp --build” to restart Docker container.</p>
                <p>If you need to restore database, instruction is provided in Admin Managers’ Functions section at <a href="#loadBackup">Load database backup</a>. </p>
                <p>In case you encounter an issue and require the rebuilding of the Docker container, here is the <a href="/docs/communication/UBCO Capstone Summer 2023 - Rebuild Docker Containers Guide.pdf" download>guide</a> .</p>
            </section>
            <br>
            <section>
                <h2><a id="admin" href="#content">Admin’s Functions</a></h2>
                <p>In addition to manager’s functions, the admin manager has extra functions under the Functions tab. The followings are the main features:</p>
                <h3>Database and Backup Related Functions</h3>
                <div class="image-container" id="database">
                <?php
                generateFigureElement('loadBackup', 'load_backup.png', 'load database backup', 'load_backup.gif', 'Load Database Backup');
                generateFigureElement('downloadBackup', 'backup_database.png', 'backup database', 'backup_database.gif', 'Download Backup Database');
                generateFigureElement('uploadBackup', 'upload_database_backup.png', 'upload database backup', 'upload_database_backup.gif', 'Upload Database Backup');
                ?>
                </div>
                <h3>Import CSV</h3>
                <div class="image-container" id="csv">
                <?php
                generateFigureElement('employeeCSV', 'upload_employee_csv.png', 'import employee csv', 'upload_employee_csv.gif', 'Import Employee CSV',"This function should only be done once.\n CSV format to follow: 3 lines of header and 3 lines of footer");
                generateFigureElement('jobCSV', 'upload_job_csv.png', 'import job csv', 'upload_job_csv.gif', 'Import Job CSV', "This function should only be done once.\n CSV format to follow: 3 lines of header and 3 lines of footer");
                ?>
                </div>
                <h3>Other Functions</h3>
                <div class="image-container" id="othersAdmin">
                <?php
                generateFigureElement('changeAdminPassword', 'change_admin_password.png', 'change admin password', 'change_admin_password.gif', 'Change Admin Password','Note: this change password function goes more security check than change manager password');
                generateFigureElement('resetManagerPassword', 'reset_manager_password.png', 'reset manager password', 'reset_manager_password.gif', 'Reset Manager Password');
                generateFigureElement('changeProjectorPassword', 'change_projector_password.png', 'change projector password', 'change_projector_password.gif', 'Change Projector Password');
                generateFigureElement('createAccount', 'create_account.png', 'create account', 'create_account.gif', 'Create Account');
                ?>
                </div>
            </section>
            <br>
            <section>
                <h2><a id="contact" href="#content">Contact and Support</a></h2>
                    <p>Please contact Next Generation Computers Martin(He/Him/His: deployment/IP address, etc) at Martin@ngcit.ca and/or Adam Fipke(He/Him/His: project developer; EMERGENCY ONLY) at adamfipke@gmail.com</p>
            </section>
            <br>
            <section>
                <h2><a id="start" href="#content">Getting Started</a></h2>
                <p>Access the Website: use Chrome or Edge for optimal performance</p>
                <p>Create Account: only admin manager can <a href="#createAccount">create new account</a></p>
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
            <button onclick="topFunction()" id="topBtn" title="go to top"><img src="/labour/src/img/toTop.svg" alt="to top">Back to Top</button>
        </div> -->
    </body>
</html>