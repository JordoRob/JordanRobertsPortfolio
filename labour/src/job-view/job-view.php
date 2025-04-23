<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
// Redirect to login page if only logged in as projector
if (isset($_SESSION['projector'])) unset($_SESSION['projector']); 
security_check_and_connect();

include_once($_SERVER['DOCUMENT_ROOT'] . "/labour/src/global_generators.php");
?>

<html>

<head>
    <link rel="stylesheet" href="/labour/src/global_styles/styles.css">
    <link rel="stylesheet" href="/labour/src/global_styles/main-content.css">
    <link rel="stylesheet" href="/labour/src/global_styles/navbar.css">
    <link rel="stylesheet" href="/labour/src/global_styles/overlay_styles.css">
    <link rel="stylesheet" href="/labour/src/Employee Page/employee_styles.css">
    <link rel="stylesheet" href="job_styles.css">
    <script src="/labour/src/chart.umd.min.js"></script>
    <script src="/labour/src/chartjs-plugin-annotation.min.js"></script>
    <script src="/labour/src/jquery-3.7.0.min.js"></script>
    <script src="drag-drop.js"></script>
    <script src="job-view.js"></script>
    <script src="job-validation.js"></script>
    <script src="/labour/src/job-details/job-info.js"></script>
    <script src="../employee info overlay/employee-info.js"></script>
    <script src="/labour/src/global_functions.js"></script>
    <script src="./custom_order/custom_order.js"></script>
    <?php echo "<script>
    localStorage.setItem('timer', ".$_SESSION['timer'].");
    </script>";?>
</head>

<body>
    <!-- Add job modal (popup overlay) -->
    <div id='job-main-overlay' class="main-overlay d-none add-job-modal">
        <img src="/labour/src/img/close.svg" class="close_overlay" onclick="hideAddJobModal()">
        <h2 id='job-form-title' class='fs-1'> Add Job </h2>
        <!-- Form for submitting new job. Currently, only required fields is title, start date (which is defaulted to now), and expected end date -->
        <form id='job-form' novalidate>
            <div class='job-add-body'>
                <div id='add-job-title-container' class=" overlay_info_box has-validation">
                    <span for="job-title" class="">Title</span>
                    <input type="text" class="form-input" id="job-title" name="title" placeholder="" required
                        pattern=".{1,255}" title="Must be filled and less than 255 characters"
                        aria-describedby="job-title-error">
                    <div class="invalid-feedback" id="job-title-error">Invalid Title</div>
                    <!-- these "invalid X" messages are placeholders, target and place in your own -->
                </div>
                <div class="overlay_info_box has-validation">
                    <span for="job-manager" class="">Project Manager</span>
                    <input type="text" class="form-input" id="job-manager" name="manager" placeholder=""
                        pattern=".{0,255}" title="Must be less than 255 characters"
                        aria-describedby="job-manager-error">
                    <div class="invalid-feedback" id="job-manager-error">Invalid Project Manager</div>
                </div>
                <div class="overlay_info_box has-validation">
                    <span for="job-address" class="">Address</span>
                    <input type="text" class="form-input" id="job-address" name="address" placeholder=""
                        pattern=".{0,255}" title="Must be less than 255 characters"
                        aria-describedby="job-address-error">
                    <div class="invalid-feedback" id="job-address-error">Invalid Address</div>
                </div>
                <div class="overlay_info_box has-validation">
                    <span for="job-start" class="">Start Date</span>
                    <input type="month" class="form-input" id="job-start" name="start_date" placeholder=""
                        title="Must be filled and before end date" aria-describedby="job-start-error">
                    <div class="invalid-feedback" id="job-start-error">Invalid Start Date</div>
                </div>
                <div class="overlay_info_box has-validation ">
                    <span for="job-end" class="">Expected End Date</span>
                    <input type="month" class="form-input" id="job-end" name="end_date" placeholder=""
                        title="Must be filled and after start date" aria-describedby="job-end-error">
                    <div class="invalid-feedback" id="job-end-error">Invalid End Date</div>
                </div>
            </div>
            <div id='add-job-hint'>
                <span id="add-job-hint-text">Hint: Click on the calendar </span>
                <span id="add-job-hint-arrow">⤷</span>
            </div>
            <div class="form-buttons">
                <!-- hitting cancel will reset the form, clicking off the form or clicking X button will not reset form (to make it more forgiving) -->
                <button id='job-reset' type='button' class='btn-cancel hover-cancel m-2'>Cancel</button>
                <button id='add-job-submit' type='submit' class='btn-confirm hover-confirm m-2'>Save</button>
            </div>
        </form>
    </div>
    <div id="job-overlay_mask" class="overlay_mask d-none add-job-modal-overlay" onclick="hideAddJobModal()"></div>

    <?php
    echo generate_nav_bar(1, true);
    ?>
    <div class='main-body'>
        <div class='edit-bar'>
            <!-- blur box required as css blur doesn't work on child elements if parent element has it -->
            <div class="blur-box"></div>
            <span class="edit-filter">
                <div class='search-wrapper'>
                    <img src='/labour/src/img/Interface/search.svg'>
                    <input class='nav-search' type='text' name='search'>
                </div>
                
                <!-- Edit custom order -->
                <button type='button' id='custom_edit' title="Edit Custom Order" onclick="show_custom_edit()">
                    <img src='/labour/src/img/Interface/reorder.svg'>
                </button>

                <!-- sort value -->
                <div class='dropdown' id='sort_filter'>
                    <button type='button' title="Filter Jobs" class='dropbtn'><img
                            src='/labour/src/img/Interface/filter.svg'></button>
                    <div class='dropdown-content'>
                        <a href='#' name='sort' data-value="custom" class='filter-sort' id="custom_order_filter">Custom
                            Order</a>
                        <a href='#' name='sort' data-value="title" class='filter-sort'>Name</a>
                        <a href='#' name='sort' data-value="manager_name" class='filter-sort'>Manager</a>
                        <a href='#' name='sort' data-value="emp" class='filter-sort'>Employees</a>
                        <a href='#' name='sort' data-value="search_start_date" class='filter-sort'>Start Date</a>
                    </div>
                </div>

                <!-- ascending/descending -->
                <div class='dropdown' id='order_filter'>
                    <button type='button' title="Order Jobs" class='dropbtn'><img
                            src='/labour/src/img/Interface/sort.svg'></button>
                    <div class='dropdown-content'>
                        <a href='#' name='order' data-value="asc" class='filter-sort'>Ascending</a>
                        <a href='#' name='order' data-value="desc" class='filter-sort'>Descending</a>
                    </div>
                </div>
                <button type='button' id='refresh_button' title="Refresh Page">
                    <img id='refresh_icon' src='/labour/src/img/Interface/refresh.svg'></img>
                    <p id='refresh_counter'></p>
                </button>
            </span>

            <span class='edit-tools'>
                <!-- Add job button -->
                <button type='button' id='add' title='Add New Job' data-bs-toggle='modal'
                    data-bs-target='#add-job-modal-adsf' onclick="showAddJobModal()">
                    <img src='/labour/src/img/Interface/plus.svg'>
                    <div class='edit_tools_title'>
                        <span>Add Job</span>
                        <div>
                </button>

                <button type='button' id='emp_overlay_toggle' title="View Employees">
                    <img src='/labour/src/img/Interface/user.svg'>
                    <div class='edit_tools_title'>
                        <span>Employee List</span>
                        <div>
                </button>
            </span>
        </div>

        <div id='emp-overlay' ondrop='drop_employee(event)' ondragover='allowDrop(event)'>
            <nav id='emp-overlay-nav'>
                <a href='#' class='overlay-tab selected-nav' id='selected-overlay' data-overlay_id=0>Unassigned</a>
                <a href='#' class='overlay-tab' data-overlay_id=1>All</a>
            </nav>
            <div class='search-wrapper'>
                <img src="/labour/src/img/Interface/search.svg">
                <input name='overlay-search' value="">
            </div>
            <div id='overlay-content' class='overlay-content'>
                <p id='overlay-error'></p>
            </div>
        </div>

        <div class='main_content' id='job-wrapper'>
            <div id='job-listings'></div>
        </div>
    </div>
    
    <footer>
        <div id='inactive-employees' class='arrow' title='Click to toggle Inactive/In School Employees'>
            <span id='inactive_wrapper' class='fill_width'>            
                <div class='inactive_title' onclick='$(".inactive-container").slideToggle();'>Inactive</div>
                <div class='inactive-container' style='display: none;' >
                    <div id='inactive-listings' class='employee_wrapper active_status_wrappers' data-job_id='-1' data-active_code='1'  ondrop='active_drop_employee(event)' ondragover='allowDrop(event)'></div>
                </div> 
            </span>
            <span id='in_school_wrapper' class='fill_width'>
                <div class='inactive_title' onclick='$(".inactive-container").slideToggle();'>In School</div>
                <div class='inactive-container' style='display: none;' >
                    <div id='in-school-listings' class='employee_wrapper active_status_wrappers' data-job_id='-1' data-active_code='2'  ondrop='active_drop_employee(event)' ondragover='allowDrop(event)'></div>
                </div> 
            </span>
        </div>
        <div class='totals-wrapper'></div>
    </footer>

    <div id="message_container" style="display: none;"></div>
    <div id="tooltip_popup" style="display: none;">Hold Ctrl and drop to duplicate</div>
</body>
<script src='/labour/src/refresh/refresh.js'></script>
<script>
    let last_check=Math.round(Date.now() / 1000); // initial check time
    refresh("job",last_check); //sets interval to re-run ajax_search(), ajax_employees(), ajax_totals() every amount of secounds
    
    // Add class ".dropdown-selected" based on session_storage. If none, default to custom order
    var sort = localStorage.getItem("sort");
    var order = localStorage.getItem("order");

    if (sort == null) {
        sort = "custom";
    }
    if (order == null) {
        order = "asc";
    }

    $("#sort_filter .dropdown-content").children(`[data-value=${sort}]`).addClass("dropdown-selected");
    $("#order_filter .dropdown-content").children(`[data-value=${order}]`).addClass("dropdown-selected");

    // Populate elements
    ajax_search();
    ajax_employees(); // pre-load the employee list
    ajax_totals();
    job_inactive_update();

    var emp_shown = false;

    // On button click, make toggle the visibility of the employee list
    $("#emp_overlay_toggle").click(function () {
        emp_shown = !emp_shown;
        if (emp_shown) {
            $("#emp_overlay_toggle").css('background-color', '#5fb5ff');
        } else {
            $("#emp_overlay_toggle").css('background-color', '');
        }
        $("#emp-overlay").css('transition', 'unset');
        $("#emp-overlay").slideToggle("fast", function () {
            $("#emp-overlay").css('transition', '');
        });
        ajax_employees();
    });

    // When you switch tabs in the overlay, switch tabs and make the ajax call
    $(".overlay-tab").click(function () {
        $("#selected-overlay").removeClass("selected-nav");
        $("#selected-overlay").attr('id', "");
        $(event.target).attr('id', "selected-overlay");
        $(event.target).addClass("selected-nav");
        ajax_employees();
    });

    var delayTimer;
    // Search employees in overlay
    $("input[name=overlay-search]").keyup(function () {
        clearTimeout(delayTimer);
        delayTimer = setTimeout(ajax_employees, 150);
    });

    // Search job listings with filter/sort and search
    $("input[name=search]").keyup(function () {
        clearTimeout(delayTimer);
        delayTimer = setTimeout(ajax_search, 150);
    });

    // Perform ajax search on filter dropdown click
    $(".filter-sort").click(function () {
        ajax_search();
        // Set session storage to save filter/sort
        localStorage.setItem("sort", $("#sort_filter .dropdown-selected").attr("data-value"));
        localStorage.setItem("order", $("#order_filter .dropdown-selected").attr("data-value"));
    });

    $(".inactive_title").on('click', function () {
        $(".inactive_title").toggleClass('open');
        if ($(".inactive_title").hasClass('open')) {
            $('#job-wrapper').css('height', 'calc(100vh - 25em)');
            $('#emp-overlay').css('height', 'calc(100vh - 25em)');
        } else {
            $('#job-wrapper').css('height', '');
            $('#emp-overlay').css('height', '');
        }
        
    });
</script>

</html>