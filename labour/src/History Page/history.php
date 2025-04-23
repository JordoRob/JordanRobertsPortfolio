<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
// Redirect to login page if only logged in as projector
if (isset($_SESSION['projector']))
    unset($_SESSION['projector']);
security_check_and_connect();

include_once($_SERVER['DOCUMENT_ROOT'] . "/labour/src/global_generators.php");
?>
<html>

<head>
    <link rel="stylesheet" href="/labour/src/global_styles/styles.css">
    <link rel="stylesheet" href="/labour/src/global_styles/main-content.css">
    <link rel="stylesheet" href="/labour/src/global_styles/navbar.css">
    <link rel="stylesheet" href="/labour/src/job-view/job_styles.css">
    <link rel="stylesheet" href="/labour/src/global_styles/overlay_styles.css">
    <link rel="stylesheet" href="/labour/src/Employee Page/employee_styles.css">
    <link rel="stylesheet" href="history_styles.css">
    <script src="/labour/src/chart.umd.min.js"></script>
    <script src="/labour/src/chartjs-plugin-annotation.min.js"></script>
    <script src="/labour/src/jquery-3.7.0.min.js"></script>
    <script src="job-validation-history.js"></script>
    <script src="history.js"></script>
    <script src="/labour/src/job-details/job-info.js"></script>
    <script src="../employee info overlay/employee-info.js"></script>
    <script src="/labour/src/global_functions.js"></script>
</head>

<body>
    <?php
    echo generate_nav_bar(2, true);
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

                <!-- sort value -->
                <div class='dropdown' id='sort_filter'>
                    <button type='button' title="Filter Jobs" class='dropbtn'><img src='/labour/src/img/Interface/filter.svg'></button>
                    <div class='dropdown-content'>
                        <a href='#' name='sort_history' data-value="title" class='filter-sort'>Name</a>
                        <a href='#' name='sort_history' data-value="manager_name" class='filter-sort'>Manager</a>
                        <a href='#' name='sort_history' data-value="search_start_date" class='filter-sort'>Start Date</a>
                    </div>
                </div>

                <!-- ascending/descending -->
                <div class='dropdown' id='order_filter'>
                    <button type='button' title="Order Jobs" class='dropbtn'><img src='/labour/src/img/Interface/sort.svg'></button>
                    <div class='dropdown-content'>
                        <a href='#' name='order_history' data-value="asc" class='filter-sort'>Ascending</a>
                        <a href='#' name='order_history' data-value="desc" class='filter-sort'>Descending</a>
                    </div>
                </div>

                
                <!-- Page selector -->
                <button type='button' title="Select Page" id="page_select">
                    <div class="edit-tools-title">Page</div>
                    <input type="number" id="page-input" name="page-input" value="1" min="1" >
                </button>
                
                <!-- Jobs Per Page selector -->
                <button type='button' title="Jobs Per Page" id="per_page">
                    <div class="edit-tools-title">Per Page</div>
                    <input type="number" id="per-page-input" name="per-page-input" value="7" min="5" max="100">
                </button>

                <!-- Archived/All jobs toggle -->
                <div class="toggle">
                    <input type="checkbox" id="archived-all-toggler" class="togglecheckbox"  onchange="ajax_search()">
                    <label for="archived-all-toggler" class="toggle-label">All Jobs?</label>
                </div>
            </span>

            <span class='edit-tools'>
                <button type='button' title="Change Start Date" id='change-start-date'>
                    <div class="edit-tools-title">Start Date</div>      
                    <input type="month" id="start-date-input" name="start-date-input" onchange="ajax_search()" value="<?php echo date_create('now')->modify('-18 month')->format('Y-m'); ?>" min="2000-01" max="<?php echo date("Y-m"); ?>">
                </button>
                <button type='button' title="Change End Date" id='change-end-date'>
                    <div class="edit-tools-title">End Date</div>
                    <input type="month" id="end-date-input" name="end-date-input" onchange="ajax_search()" value="<?php echo date_create('now')->modify('+18 month')->format('Y-m'); ?>" min="2000-01" max="<?php echo date_create('now')->modify('+18 month')->format('Y-m'); ?>">
                </button>
            </span>
        </div>

        <!-- <canvas id='graph'></canvas> -->

        <div class='main_content' id='job-wrapper'>
            <div id='job-listings'></div>
        </div>

        <div id='emp-overlay'>
            <div class='search-wrapper'>
                <img src="/labour/src/img/Interface/search.svg">
                <input name='overlay-search' value="">
            </div>
            <div id='overlay-content' class='overlay-content'>
                <p id='overlay-error'></p>
            </div>
        </div>


    </div>

    <div id="message_container" style="display: none;"></div>
</body>

<script>
    var num_jobs = <?php 
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM job");
        $stmt->execute();
        $num_jobs = $stmt->fetchColumn();
        echo $num_jobs;
    ?>;
    var num_archived_jobs = <?php 
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM job WHERE archived IS NOT NULL");
        $stmt->execute();
        $num_jobs = $stmt->fetchColumn();
        echo $num_jobs;
    ?>;

    // Add event listener page selector max = num_jobs / jobs per page
    document.getElementById("page-input").addEventListener("change", function() {
        
        var max = Math.ceil(($("#archived-all-toggler").prop("checked") ? num_jobs : num_archived_jobs) / document.getElementById("per-page-input").value);
        if (this.value > max) {
            this.value = max;
        }
        ajax_search();
    });

    document.getElementById("per-page-input").addEventListener("change", function() {
        var max = Math.ceil(($("#archived-all-toggler").prop("checked") ? num_jobs : num_archived_jobs) / document.getElementById("per-page-input").value);
        if (document.getElementById("page-input").value > max) {
            document.getElementById("page-input").value = max;
        }
        ajax_search();
    });

    // Add class ".dropdown-selected" based on session_storage. If none, default to start date ascending
    var sort = localStorage.getItem("sort_history");
    var order = localStorage.getItem("order_history");

    if (sort == null) {
        sort = "search_start_date";
    }
    if (order == null) {
        order = "asc";
    }

    $("#sort_filter .dropdown-content").children(`[data-value=${sort}]`).addClass("dropdown-selected");
    $("#order_filter .dropdown-content").children(`[data-value=${order}]`).addClass("dropdown-selected");

    // Populate elements
    ajax_search();

    var emp_shown = false;

    // On button click, make toggle the visibility of the employee tab
    $("#emp_overlay_toggle").click(function () {
        emp_shown = !emp_shown;
        if (emp_shown) {
            $("#emp_overlay_toggle").css('background-color', '#5fb5ff');
        } else {
            $("#emp_overlay_toggle").css('background-color', '');
        }
        $("#emp-overlay").slideToggle("fast");
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
        localStorage.setItem("sort_history", $("#sort_filter .dropdown-selected").attr("data-value"));
        localStorage.setItem("order_history", $("#order_filter .dropdown-selected").attr("data-value"));
    });
</script>

</html>