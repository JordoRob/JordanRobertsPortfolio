<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
security_check_and_connect();
include_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/global_generators.php";
?>

<html>

<head>
    <link rel="stylesheet" href="/labour/src/global_styles/styles.css">
    <link rel="stylesheet" href="/labour/src/global_styles/main-content.css">
    <link rel="stylesheet" href="/labour/src/global_styles/navbar.css">
    <link rel="stylesheet" href="/labour/src/global_styles/overlay_styles.css">
    <link rel="stylesheet" href="/labour/src/Employee Page/Pin Bar Functions/pin-bar-functions.css">
    <link rel="stylesheet" href="employee_styles.css">

    <script src="/labour/src/jquery-3.7.0.min.js"></script>
    <script src="/labour/src/global_functions.js"></script>
    <script src="/labour/src/employee info overlay/employee-info.js"></script>
    <script src="drag-drop.js"></script>
    <script src="add-employee.js"></script>
    <script src="Pin Bar Functions/pin-bar.js"></script>
    <script src='/labour/src/refresh/refresh.js'></script>
</head>

<script>
    var show_archived = false;

    function ajax_search() {
        // Change dropdown selected class to clicked on element
        if (event != null) {
            e = $(event.target);
            if (e.attr("name") != "search" && e.hasClass("filter-sort")) {
                e.parent().children().removeClass("dropdown-selected");
                e.addClass("dropdown-selected");
            }
        }

        // Get emp ids from pin bar
        var emp_ids = [-1];
        $("#pin_bar").children().each(function () {
            emp_ids.push($(this).data("emp_id"));
        });

        let search_term =  $("input[name=search]").val();

        $.ajax({
            type: "POST",
            url: "generate_list.php",
            data: {
                search: search_term,
                sort: $("a[name=sort].dropdown-selected").data("value"),
                order: $("a[name=order].dropdown-selected").data("value"),
                pinned_emp_ids: emp_ids,
                show_archived: show_archived
            },
            success: function (data) {
                $("#search_results").html(data); // Populate results div with ajax response
                let pattern = new RegExp("(" +  search_term + ")", "gi");
                let itemsToSearch = $(".job_info h1, .employee_details h1");
                itemsToSearch.each(function () {
                    let src_str = $(this).text();
                    src_str = src_str.replace(pattern, "<mark>$1</mark>");
                    src_str = src_str.replace(/(<mark>[^<>]*)((<[^>]+>)+)([^<>]*<\/mark>)/, "$1</mark>$2<mark>$4");
                    $(this).html(src_str);
                });
                // $(".employee").find("h1").each(function () {
                //     if (this.scrollWidth > this.clientWidth) {
                //         $(this).addClass("long-name");
                //         $(this).html("&nbsp");
                //     }
                // }); 
            },
            error: function (jqXHR, textStatus, errorThrown) {
                send_message(jqXHR.responseText);
                console.log("An error occurred during search query: " + textStatus);
            }
        });
    }

    $(document).ready(function () {
        // Perform ajax search and hide dropdown on filter dropdown click
        $(".filter-sort").click(ajax_search);

        // Delay ajax call until user stops typing for 500ms
        var delayTimer;
        $("input[name=search]").keyup(function () {
            clearTimeout(delayTimer);
            delayTimer = setTimeout(ajax_search, 150);
        });

        // Reactive unpin box when employee is dragged over
        document.getElementById('unpin_box').addEventListener(
            "dragenter",
            function () {
                $("#pinbutton").attr("src", "../img/Interface/X-open.svg");
                document.getElementById('pin_message').style.color = "rgb(213, 32, 32)";
                document.getElementById('pin').style.background = "rgb(213, 32, 32)";
            }
        );
        document.getElementById('unpin_box').addEventListener(
            "dragleave",
            function () {
                $("#pinbutton").attr("src", "../img/Interface/X-closed.svg");
                document.getElementById('pin_message').style.color = "black";
                document.getElementById('pin').style.background = "white";
            }
        );
    });
</script>

<body>
    <?php
    echo generate_nav_bar(0, true);
    ?>
    <div class='main-body'>
        <!-- Function Bar -->
        <div class="edit-bar">
            <div class="blur-box"></div>
            <span class="edit-filter">
                <!-- Search box -->
                <div class='search-wrapper'>
                    <img src='../img/Interface/search.svg'>
                    <input type="text" class='nav-search' name="search" id='employee-search'>
                </div>
                <!-- Sort dropdown -->
                <div class='dropdown'>
                    <button type='button' id='sort' title="Filter Employees" class='dropbtn'><img
                            src='../img/Interface/filter.svg'></button>
                    <div class='dropdown-content'>
                        <a name='sort' data-value="role" class='filter-sort dropdown-selected'>Position</a>
                        <a name='sort' data-value="name" class='filter-sort'>Name</a>
                        <a name='sort' data-value="hired" class='filter-sort'>Date</a>
                    </div>
                </div>

                <!-- ascending/descending -->
                <div class='dropdown'>
                    <button type='button' id='order' title="Order Employees" class='dropbtn'><img
                            src='../img/Interface/sort.svg'></button>
                    <div class='dropdown-content'>
                        <a name='order' data-value="asc" class='filter-sort dropdown-selected'>Ascending</a>
                        <a name='order' data-value="desc" class='filter-sort'>Descending</a>
                    </div>
                </div>
                <button type='button' id='refresh_button' title="Refresh Page">
                    <img id='refresh_icon' src='/labour/src/img/Interface/refresh.svg'></img>
                    <p id='refresh_counter'></p>
                </button>
            </span>

            <span class="edit-tools">
                <!-- Add employee button -->
                <button id="add_employee" title='Add Employee' onclick='add_emp_overlay()'>
                    <img src='../img/Interface/user-add.svg'>
                    <div class='edit_tools_title'>
                        <span>Add Employee</span>
                        <div>
                </button>

                <!-- Toggle show archive -->
                <button id="show_archived" title='Show Archived Employees' onclick="
                        show_archived = !show_archived; 
                        ajax_search();
                        if (show_archived) {
                            $(this).css('background-color', '#5fb5ff');
                        } else {
                            $(this).css('background-color', '');
                        }
                    ">
                    <img src='/labour/src/img/Interface/archive.svg'>
                    <div class='edit_tools_title'>
                        <span>Show Archived</span>
                        <div>
                </button>
            </span>
        </div>
        <!-- Employee list (Search results) generated by ajax_search function -->
        <div class="main_content">
            <div id="search_results">
                <script>
                    ajax_search();

                    // Generated section header calls this when clicked to toggle section
                    function minimize(target) {
                        $("div[name=section" + target + "]").parent().slideToggle();
                        let arrow = $("#section_title_" + target).children(".arrow");
                        if (arrow.html() == "▼") {
                            arrow.html("►");
                        } else {
                            arrow.html("▼");
                        }
                    }
                    var last_check = Math.round(Date.now() / 1000); // initial check time
                    refresh("employee", last_check); //sets interval to re-run ajax_search() every amount of secounds
                </script>
            </div>
        </div>

    </div>
    <!-- Pin Bar -->
    <div id="pin_bar_main">
        <div class="blur-box"></div>
        <div class="dropdown">
            <button type="button" id="pin" title="Group Actions" class="dropbtn"><img src="../img/Interface/pin.svg"
                    id='pinbutton'></button>
            <div class="dropdown-content pin_dropdown">
                <a name="pin" id="assign" onclick="batch_assign()">Assign to job...</a>
                <a name="pin" id="unpin_all" onclick="unpin_all()">Unpin All</a>
            </div>
        </div>
        <!-- Hidden "unpin" box where can be dragged -->
        <div id="unpin_box" ondrop="unpin_employee(event)" ondragover="allowDrop(event)"></div>
        <div id="pin_message">Drag here or Ctrl + Click employees to pin...</div>
        <div id="pin_bar" class="employee_wrapper" data-job_id='-1' ondrop='drop_pin_employee(event)'
            ondragover='allowDrop(event)'></div>
    </div>

    <div id="message_container" style="display: none;"></div>

    <body>

</html>