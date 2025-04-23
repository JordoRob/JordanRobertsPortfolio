document.write("<script src='/labour/src/global_data_table.js'></script>");
var detect_ctrl = false;

/**
*  Generates an employee for the given id
* @param {Int} emp_id - Employees id
*/
function employeeDetails(emp_id) {
    if (detect_ctrl) {
        ctrl_pin_employee(emp_id);
        return;
    }
    $.ajax({
        type: "POST",
        url: "/labour/src/employee info overlay/emp-info-overlay.php",
        data: {
            emp_id: emp_id
        },
        success: function (data) {
            generate_overlay();
            $("#main-overlay").html(data); // Populate results div with ajax response
        },
        error: function (jqXHR, textStatus, errorThrown) {
            send_message(`An error occurred while loading the employee details.\n${jqXHR.responseText}`);
            console.log("An error occurred during search query: " + textStatus);
        }
    });
}

/**
*  Changes the overlay to inputs and dropdowns
*/
function enableEdit() {
    var emp_id = $(".info-wrapper").data("emp_id");

    // Change to text boxes
    $("#name_datafield").replaceWith("<input class='info-name' type='text' id='name_datafield' placeholder='Employee Name' value='" + escapeHTML($("#name_datafield").text()) + "' />");
    $("#phone_datafield").replaceWith("<input type='tel' id='phone_datafield' pattern='[0-9]{3}-[0-9]{2}-[0-9]{3}' value='" + escapeHTML($("#phone_datafield").text()) + "' />");
    $("#phone_datafield_secondary").replaceWith("<input type='tel' id='phone_datafield_secondary' pattern='[0-9]{3}-[0-9]{2}-[0-9]{3}' value='" + escapeHTML($("#phone_datafield_secondary").text()) + "' />");
    $("#email_datafield").replaceWith("<input type='email' id='email_datafield' value='" + escapeHTML($("#email_datafield").text()) + "' />");
    $("#datehired_datafield").replaceWith("<input type='date' id='datehired_datafield' value='" + $("#datehired_datafield").text() + "' />");
    $("#datearchived_datafield").replaceWith("<input type='date' id='datearchived_datafield' value='" + $("#datearchived_datafield").text() + "' />");
    $("#birthday_datafield").replaceWith("<input type='date' id='birthday_datafield' value='" + $("#birthday_datafield").text() + "' />");
    $("#notes_datafield").removeAttr("disabled");
    $('#overlay_delete').css("visibility", "visible");

    var data_title = $("#info-title").attr("data-title");
    var generated_select = "<select id='info-title' class='info-subheading'>";
    for (var i = 0; i < roles.length; i++) {
        generated_select += "<option value=" + i;
        if (i == data_title) {
            generated_select += " selected";
        }
        generated_select += ">" + roles[i] + "</option>";
    };

    $("#info-title").replaceWith(generated_select + "</select>");

    var currentStatus = $("#status-datafield").data("statuscode");
    var status_html = generate_status_options(currentStatus);
    var status = "<div class='dropdown status-drop'>" + status_html[0] + "<div class='dropdown-content' id='status-dropdown-content'>" + status_html[1] + "</div></div>";

    $("#status-datafield").replaceWith(status);
    $(".status-drop").click(function () {
        // Closes all other dropdowns when mouse is clicked on a dropdown button
        $(".dropdown-content").not($(this).children(".dropdown-content")).slideUp("fast");
        $(".status-drop").toggleClass('open');
        // Toggles the current dropdown when mouse is clicked on a dropdown button
        $(this).children(".dropdown-content").slideToggle("fast");
    });

    // Change image
    $(`#overlay_edit_emp_${emp_id}`).attr("src", "/labour/src/img/save.svg");
    $(`#overlay_edit_emp_${emp_id}`).attr("title", "Save changes");
    $(`#overlay_edit_emp_${emp_id}`).attr("onclick", "saveEdit()");
    document.getElementById('phone_datafield').addEventListener('input', function (e) {
        phoneMask(e);
    });
    document.getElementById('phone_datafield_secondary').addEventListener('input', function (e) {
        phoneMask(e);
    });
    // Fade save button background color every second
    var flip = false;
    fade_emp = setInterval(
        function () {
            if (flip) {
                $(`#overlay_edit_emp_${emp_id}`).css("background-color", "");
                flip = false;
            } else {
                $(`#overlay_edit_emp_${emp_id}`).css("background-color", "#d23127");
                flip = true;
            }
        },
        1000
    );
}
/**
*  Remakes the dropdown with new selection
* @param {object} el - newly selected node
*/
function statusChange(el) {
    var selected = $(el);
    var status_html = generate_status_options(selected.data('statuscode'));
    $("#status-datafield").replaceWith(status_html[0]);
    $("#status-dropdown-content").html(status_html[1]);

}
/**
* Generates an array with 0 being currently selected node and 1 being a list of options
* @param {Int} currentStatus - Currently selected node
*/
function generate_status_options(currentStatus) { //creates a current node then loops through an array 
    var current = "<div id='status-datafield' class='overlay_card card-" + active_status[currentStatus].replace(/\s+/g, '-') + "' data-statuscode=" + currentStatus + ">" +
        active_status[currentStatus] + "</div>";
    var options = "";
    for (var key in active_status) {
        if (key != currentStatus) {
            options += "<div data-statuscode=" + key + " class='overlay_card card-" + active_status[key].replace(/\s+/g, '-') + " dropdown-card' onclick='statusChange(this)'>" + active_status[key] + "</div>";
        }
    }
    return ([current, options]);
}
/**
* Saves changes made on emp overlay
*/
async function saveEdit() {
    // Stop fade
    clearInterval(fade_emp);


    var emp_id = $(".info-wrapper").data("emp_id");
    var origin_active_code = $(".info-wrapper").data('active');
    var new_active_code = $("#status-datafield").data("statuscode");


    var cont = true;
    if (origin_active_code != new_active_code) {
        cont = await employee_assignment_handler(emp_id, origin_active_code, new_active_code); //Make them select options and confirm inactive choices
    }
    if (cont) {
        $(`#overlay_edit_emp_${emp_id}`).attr("src", "/labour/src/img/loading.svg");
        $(`#overlay_edit_emp_${emp_id}`).attr("title", "Saving...");
    $(`#overlay_edit_emp_${emp_id}`).attr("onclick", "");
        $.ajax({
            type: "POST",
            url: "/labour/src/employee info overlay/update_employee.php",
            async: false,
            data: {
                emp_id: emp_id,
                name: $("#name_datafield").val(),
                phone: $("#phone_datafield").val(),
                phoneSec: $("#phone_datafield_secondary").val(),
                email: $("#email_datafield").val(),
                datehired: $("#datehired_datafield").val(),
                datearchived: $("#datearchived_datafield").val(),
                birthday: $("#birthday_datafield").val(),
                notes: $("#notes_datafield").val(),
                title: $("#info-title").val(),
                active: $("#status-datafield").data("statuscode")
            },
            success: function (data) {
                $.ajax({
                    type: "POST",
                    url: "/labour/src/employee info overlay/emp-info-overlay.php",
                    async: false,
                    data: {
                        emp_id: emp_id
                    },
                    success: function (data) {
                        $("#main-overlay").html(data); // Populate results div with ajax response
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        send_message(`An error occurred while loading the employee details.\n${jqXHR.responseText}`);
                        console.log("An error occurred during search query: " + textStatus);
                    }
                });
                $(`#overlay_edit_emp_${emp_id}`).attr("src", "/labour/src/img/success.svg");
                $(`#overlay_edit_emp_${emp_id}`).css("background-color", "green");
                $(`#overlay_edit_emp_${emp_id}`).attr("onclick", "");
                // Set timer to change back to edit button
                setTimeout(
                    function () {
                        clearInterval(fade_emp);
                        $(`#overlay_edit_emp_${emp_id}`).attr("src", "/labour/src/img/edit.svg");
                        $(`#overlay_edit_emp_${emp_id}`).attr("onclick", "enableEdit()");
                        $(`#overlay_edit_emp_${emp_id}`).css("background-color", "");
                        $(`#overlay_edit_emp_${emp_id}`).attr("title", "Edit employee details");
                },
                    3000
                );

                // No lover needed as regernates overlay

            // Change back to text
            // $("#name_datafield").replaceWith("<span class='info-name' id='name_datafield'>" + $("#name_datafield").val() + "</span>");
            // $("#phone_datafield").replaceWith("<span id='phone_datafield'>" + $("#phone_datafield").val() + "</span>");
            // $("#phone_datafield_secondary").replaceWith("<span id='phone_datafield_secondary'>" + $("#phone_datafield_secondary").val() + "</span>");
            // $("#email_datafield").replaceWith("<span id='email_datafield'>" + $("#email_datafield").val() + "</span>");
            // $("#datehired_datafield").replaceWith("<span id='datehired_datafield'>" + $("#datehired_datafield").val() + "</span>");
            // $("#datearchived_datafield").replaceWith("<span id='datearchived_datafield'>" + $("#datearchived_datafield").val() + "</span>");
            // $("#birthday_datafield").replaceWith("<span id='birthday_datafield'>" + $("#birthday_datafield").val() + "</span>");
            // $("#notes_datafield").attr("disabled", "disabled");
            // $("#info-title").replaceWith("<span id='info-title' class='info-subheading' data-title='" + $("#info-title").val() + "'>" + $("#info-title option:selected").text() + "</span>");
            // $('#overlay_delete').css("visibility", "hidden");
            // $('#overlay_archive').css("visibility", "hidden");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(`#overlay_edit_emp_${emp_id}`).attr("src", "/labour/src/img/error.svg");
            $(`#overlay_edit_emp_${emp_id}`).css("background-color", "red");
            // Set timer to change back to edit button
            setTimeout(
                function () {
                    $(`#overlay_edit_emp_${emp_id}`).attr("src", "/labour/src/img/save.svg");
                    $(`#overlay_edit_emp_${emp_id}`).attr("onclick", "saveEdit()");
                },
                3000
            );

                send_message("An error occurred: " + jqXHR.responseText, true);
            }
        });
    }
}


function change_active(emp_id, active_code) {
    $.ajax({
        url: "/labour/src/Employee Page/change-active.php",
        method: "POST",
        data: {
            emp_id: emp_id,
            drop_active_code: active_code
        },
        success: function (data) {
            send_message(data);
            ajax_search();
            // for job page functions (check if the functions exist)
            if (typeof job_inactive_update === "function") { 
                job_inactive_update();
            }
            if (typeof ajax_totals === "function") { 
                ajax_totals();
            }
            if (typeof ajax_employees === "function" && emp_shown) { 
                ajax_employees();
            }
        },
        error: function (xhr, status, error) {
            send_message(xhr.responseText);
        }
    });
}

function archiveEmployee(emp_id) {
    $.ajax({
        url: "/labour/src/Employee Page/check_emp_job_assignments.php",
        method: "POST",
        data: {
            emp_id: emp_id,
        },
        success: function () {
            change_active(emp_id, -1);
            change_cards();
        },
        error: function (xhr, status, error) {
            var jobs_assigned = JSON.parse(xhr.responseText);
            var overlay_id = "move_out_of_active_overlay";

            // Generate confimation overlay
            generate_overlay(overlay_id, "45em", "15em");

            $(`#${overlay_id}`).html(
                `
                <h1 class="info-name">Caution!</h1>
                <span id="overlay_separator_line"></span>
                <p class="overlay_notification_message">Moving this employee out of active status will also remove them from the following jobs:</p>
                <div class="overlay_notification_list"></div>
                `
            );
            for (var i = 0; i < jobs_assigned.title.length; i++) {
                $(".overlay_notification_list").append(
                    `
                    <p> ∙ ${jobs_assigned.title[i]}</p>
                    `
                );
            }
            $(`#${overlay_id}`).append(
                `
                <div class="overlay_notification_buttons">
                    <div class="overlay_button reject_hover" onclick="closeOverlay('${overlay_id}')">Cancel</div>
                    <div class="overlay_button confirm_hover" id="confirm_remove">Confirm</div>
                </div>
                <div id="overlay_ajax_message"></div>
                `
            );

            // Add event listener to confirm button
            $("#confirm_remove").click(
                function () {
                    $.ajax({
                        url: '/labour/src/Employee Page/mass_remove.php',
                        type: 'POST',
                        data: {
                            emp_id: emp_id
                        },
                        success: function (response) {
                            change_active(emp_id, -1);
                            closeOverlay(overlay_id);
                            change_cards();
                        },
                        error: function (jqXHR, status, error) {
                            $("#overlay_ajax_message").html(jqXHR.responseText);
                        }
                    });
                }
            );
        }
    });
}

function deleteEmployee(emp_id, emp_name) {
    // Generate confimation overlay
    var overlay_id = "delete_employee_overlay";
    generate_overlay(overlay_id, "45em", "13em");

    $(`#${overlay_id}`).html(
        `
        <h1 class="info-name">Caution!</h1>
        <span id="overlay_separator_line"></span>
        <p class="overlay_notification_message">
            Are you sure you want to delete this employee?
        </p>
        <p class="overlay_notification_message important_message">
            Deleting is permanent and removes their data from charts and graphs, unlike archiving.
        </p>
        `
    );

    $(`#${overlay_id}`).append(
        `
        <div class="overlay_notification_buttons">
            <div class="overlay_button reject_hover" onclick="closeOverlay('${overlay_id}')">Cancel</div>
            <div class="overlay_button confirm_hover" id="confirm_delete">Confirm</div>
        </div>
        <div id="overlay_ajax_message"></div>
        `
    );

    $("#confirm_delete").click(
        function () {
            $.ajax({
                type: "POST",
                url: "/labour/src/employee info overlay/deleteEmployee.php",
                data: {
                    id: emp_id
                },
                success: function (data) {
                    closeOverlay(overlay_id);
                    closeOverlay();
                    ajax_search();
                    send_message(`Successfully deleted ${decodeURIComponent(emp_name)}`);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    send_message(`Error deleting ${decodeURIComponent(emp_name)}: ` + jqXHR.responseText);
                    console.log(`Error deleting ${decodeURIComponent(emp_name)}: ` + jqXHR.responseText);
                }
            });
        }
    );
}

function change_cards() {
    // change class
    $(".card-Active").html("Archived");
    $(".card-Active").removeClass("card-Active").addClass("card-Archived");

    $(".card-Assigned").html("Unassigned");
    $(".card-Assigned").removeClass("card-Assigned").addClass("card-Unassigned");
}

function uploadImage(upload) {
    var fd = new FormData();
    var files = upload.files[0];
    var id = $(".info-wrapper").data('emp_id');

    fd.append('upload', files);
    fd.append('id', id);
    fd.append('immediate', true);
    $(`#overlay_edit_emp_${id}`).attr("src", "/labour/src/img/loading.svg");
    $(`#overlay_edit_emp_${id}`).attr("onclick", "");
    $.ajax({
        url: "/labour/src/employee info overlay/fileUpload.php",
        type: 'post',
        data: fd,
        contentType: false,
        dataType: "json",
        processData: false,
        success: function (data) {
            if (!data[0]) {
                // error
                send_message(data[1], true);
            } else {
                //success 
                $(`#overlay_edit_emp_${id}`).attr("src", "/labour/src/img/success.svg");
                $(`#overlay_edit_emp_${id}`).css("background-color", "green");
                // Set timer to change back to edit button
                setTimeout(
                    function () {
                        clearInterval(fade_emp);
                        $(`#overlay_edit_emp_${id}`).attr("src", "/labour/src/img/edit.svg");
                        $(`#overlay_edit_emp_${id}`).attr("onclick", "enableEdit()");
                        $(`#overlay_edit_emp_${id}`).css("background-color", "");
                    },
                    3000
                );
                var forceupdate = "?timestamp=" + new Date().getTime();
                $("#edit-employee-profile-pic").attr("src", data[1] + forceupdate);
                $(".employee[data-emp_id=" + id + "]").find("img").attr("src", data[1] + forceupdate);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(`#overlay_edit_emp_${id}`).attr("src", "/labour/src/img/error.svg");
            $(`#overlay_edit_emp_${id}`).css("background-color", "red");
            setTimeout(
                function () {
                    $(`#overlay_edit_emp_${id}`).attr("src", "/labour/src/img/edit.svg");
                    $(`#overlay_edit_emp_${id}`).attr("onclick", "enableEdit()");
                    $(`#overlay_edit_emp_${id}`).css("background-color", "");
                },
                3000
            );
            send_message(JSON.parse(jqXHR.responseText)[1], true);
        }
    });
}
function phoneMask(e) {
    var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
    e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
}


/**
* Generates confirmations and checks validity of switching employee assignment
* @param {int} emp_id - id of the employee
* @param {int} origin_active_code - initial active code of employee
* @param {int} active_code - new active code
*/
function employee_assignment_handler(emp_id, origin_active_code, active_code) {
    return new Promise((resolve) => {
        if (origin_active_code == -1) {

            var overlay_id = "notification_overlay";
            generate_overlay(overlay_id, "45em", "18em", false);
            $(`#${overlay_id}`).html(
                `
            <h1 class="info-name">Notice</h1>
            <span id="overlay_separator_line"></span>
            <p class="overlay_notification_message">You are un-archiving an employee, do you want to remove the original archive date?</p>
            <ul class="overlay_notification_info_list">
                <li class="overlay_notification_submessage">If you choose to remove the archive date, a new archive date will be assigned to this employee the next time they are archived as if they were never archived.</li>
                <li class="overlay_notification_submessage">You can choose to keep the date if you are using the archive date as a way to know when an employee was fired, for instance.</li>
                <li class="overlay_notification_submessage">Archive date is currently not being used anywhere else in the system, this is purely for if you want to keep track of when they were archived.</li>
            </ul>
            <div class="overlay_notification_buttons">
                <div class="overlay_button reject_hover" onclick="closeOverlay('${overlay_id}')" id="cancel_unarchive">Cancel</div>
                <div class="overlay_button reject_hover" id="remove_date">Remove Date</div>
                <div class="overlay_button confirm_hover" id="keep_date">Keep Date</div>
            </div>
            <div id="overlay_ajax_message"></div>
            `
            );

            $("#keep_date").click(
                function () {
                    closeOverlay(overlay_id);
                    resolve(true);
                }
            );

            $("#remove_date").click(
                function () {
                    $("#datearchived_datafield").val(null);
                    closeOverlay(overlay_id);
                    resolve(true);
                }
            );

            $("#cancel_unarchive").click(
                function () {
                    resolve(false);
                });

        }
        // check to see if user is being dropped from active area to inactive or school area
        else if (active_code != 0) {
            $.ajax({
                url: "/labour/src/Employee Page/check_emp_job_assignments.php",
                method: "POST",
                async: 'false',
                data: {
                    emp_id: emp_id,
                },
                success: function () {
                    resolve(true);
                },
                error: function (xhr, status, error) {
                    var jobs_assigned = JSON.parse(xhr.responseText);
                    var overlay_id = "move_out_of_active_overlay";

                    // Generate confimation overlay
                    generate_overlay(overlay_id, "45em", "15em", false);

                    $(`#${overlay_id}`).html(
                        `
                    <h1 class="info-name">Caution!</h1>
                    <span id="overlay_separator_line"></span>
                    <p class="overlay_notification_message">Moving this employee out of active status will also remove them from the following jobs</p>
                    <div class="overlay_notification_list"></div>
                    `
                    );
                    for (var i = 0; i < jobs_assigned.title.length; i++) {
                        $(".overlay_notification_list").append(
                            `
                        <p> ∙ ${jobs_assigned.title[i]}</p>
                        `
                        );
                    }
                    $(`#${overlay_id}`).append(
                        `
                    <div class="overlay_notification_buttons">
                        <div class="overlay_button reject_hover" onclick="closeOverlay('${overlay_id}')" id='cancel_inactive'>Cancel</div>
                        <div class="overlay_button confirm_hover" id="confirm_remove">Confirm</div>
                    </div>
                    <div id="overlay_ajax_message"></div>
                    `
                    );

                    // Add event listener to confirm button
                    $("#confirm_remove").click(
                        function () {
                            $.ajax({
                                url: '/labour/src/Employee Page/mass_remove.php',
                                type: 'POST',
                                async: 'false',
                                data: {
                                    emp_id: emp_id
                                },
                                success: function (response) {
                                    closeOverlay(overlay_id);
                                    resolve(true);
                                },
                                error: function (jqXHR, status, error) {
                                    $("#overlay_ajax_message").html(jqXHR.responseText);
                                }
                            });
                        }
                    );
                    $("#cancel_inactive").click(
                        function () {
                            resolve(false);
                        });
                }
            });
        }
    });
}