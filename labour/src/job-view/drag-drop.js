// Function to show popup above mouse cursor
function show_popup(event) {
    // Get mouse cursor position
    var x = event.clientX;
    var y = event.clientY;

    // Get the popup element
    var popup = document.getElementById("tooltip_popup");

    // Show the popup
    popup.style.display = "block";

    // Set the position of the popup
    popup.style.top = y - 60 + "px";
    popup.style.left = x - 100 + "px";
}

function drag_employee(ev) {
    // Set the effectAllowed to move
    ev.dataTransfer.effectAllowed = "move";

    // Get HTML element id of the dragged employee
    ev.dataTransfer.setData("dragged_employee_object", ev.target.id);
    let employeeWrapper = ev.target.closest('.employee_wrapper');
    if (employeeWrapper && employeeWrapper.dataset.active_code) {
        ev.dataTransfer.setData("active_code", employeeWrapper.dataset.active_code);
    }
    
    // Check if the dragged employee is from the employee list or from a job listing
    var job_id = ev.target.closest('.employee_wrapper');
    if (job_id !== null) {
        // If the dragged employee is from a job listing, get the job id
        job_id = job_id.dataset.job_id;
    } else {
        // If the dragged employee is from the employee list, set the job id to -1
        job_id = -1;
    }

    // Get the employee id
    var emp_id = ev.target.dataset.emp_id;

    // Package the job id and employee id into the dataTransfer object
    ev.dataTransfer.setData("job_id", job_id);
    ev.dataTransfer.setData("emp_id", emp_id);

    // Change colour of wrappers to indicate drop target
    // Make sure that the source wrapper is not affected
    var temp_style = document.createElement('style');
    temp_style.innerHTML = `
        .job_listing .employee_wrapper{
            background: rgb(0 255 61 / 15%) !important;
        }
        .job_listing[data-job_id="${job_id}"] .employee_wrapper{
            background: transparent !important;
        }
    `;

    // If start target isn't the employee list, change the colour of the start target
    if (job_id != -1) {
        temp_style.innerHTML += `
            #emp-overlay {
                background: rgb(0 255 61 / 15%) !important;
            }
        `;
        // Show tooltip popup
        document.addEventListener('drag', show_popup);
    }

    // Append the style element to the head
    document.head.appendChild(temp_style);

    // Add event listener to remove the style element when the drag ends
    ev.target.addEventListener(
        'dragend',
        function () {
            temp_style.innerHTML = ``;
            document.getElementById("tooltip_popup").style.display = "none";
            document.removeEventListener('drag', show_popup);
        },
        { once: true } // Delete the event listener after it is called once
    );
}

function allowDrop(ev) {
    ev.preventDefault();
}

function drop_employee(ev) {
    ev.preventDefault();
    // Set end_job_id to -1 if the drop target is the employee list
    var end_job_id;
    if ((wrapper = ev.target.closest('.employee_wrapper')) !== null) {
        end_job_id = wrapper.dataset.job_id;
    } else if ((wrapper = ev.target.closest('#emp-overlay')) !== null) {
        end_job_id = -1;
    } else {
        send_message("Invalid drop target");
        return;
    }

    var start_job_id = ev.dataTransfer.getData("job_id");

    // Check that start_job_id and end_job_id are different
    if (wrapper && end_job_id != start_job_id) {
        if (start_job_id == -1 || end_job_id == -1) {
            $("#emp-overlay").append('<div id="emp-block"><img src="/src/img/loading.svg"></div>');
        }
        var emp_id = ev.dataTransfer.getData("emp_id");

        // Check to see if Ctrl key is pressed
        if (ev.ctrlKey && end_job_id != -1) {
            // If Ctrl key is pressed, change start_job_id to -1
            start_job_id = -1;
        }
        // Perform ajax call to update the database
        $.ajax({
            url: 'move_employee.php',
            type: 'POST',
            data: {
                start_job_id: start_job_id,
                end_job_id: end_job_id,
                emp_id: emp_id
            },
            success: function (response) {
                ajax_search();
                if (start_job_id == -1 || end_job_id == -1) {
                    ajax_employees();
                    $("#emp-block").remove();
                }
                send_message(response);
                ajax_totals();
                job_inactive_update();
            },
            error: function (jqXHR, status, error) {
                send_message(jqXHR.responseText);
                $("#emp-block").remove();
            }
        });
    }

}
function active_drop_employee(ev) {
    ev.preventDefault();

    // Get nearest wrapper active code
    var active_code = ev.target.closest('.employee_wrapper').dataset.active_code;
    var emp_id = ev.dataTransfer.getData("emp_id");

    var origin_active_code = ev.dataTransfer.getData("active_code");
    var start_job_id = ev.dataTransfer.getData("job_id");

    if (start_job_id == -1) {
        send_message("Invalid drop target");
        return;
    }

    // Check to make sure that not being dropped on the same wrapper or from the pin bar
    if (origin_active_code == active_code) {
        return;
    }
    // check to see if user is being dropped from active area to inactive or school area
    if (active_code == 0) {
        change_active(emp_id, active_code);
    } else {
        $.ajax({
            url: "/src/Employee Page/check_emp_job_assignments.php",
            method: "POST",
            data: {
                emp_id: emp_id,
            },
            success: function () {
                change_active(emp_id, active_code);
            },
            error: function (xhr, status, error) {
                var jobs_assigned = JSON.parse(xhr.responseText);

                // if employee is assigned to only one job, don't show overlay (only for job page)
                if (jobs_assigned.title.length == 1) {
                    $.ajax({
                        url: '/src/Employee Page/mass_remove.php',
                        type: 'POST',
                        data: {
                            emp_id: emp_id
                        },
                        success: function (response) {
                            change_active(emp_id, active_code);
                        },
                        error: function (jqXHR, status, error) {
                            $("#overlay_ajax_message").html(jqXHR.responseText);
                        }
                    });
                } else {
                    var overlay_id = "move_out_of_active_overlay";

                    // Generate confimation overlay
                    generate_overlay(overlay_id, "45em", "15em");

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
                                url: '/src/Employee Page/mass_remove.php',
                                type: 'POST',
                                data: {
                                    emp_id: emp_id
                                },
                                success: function (response) {
                                    change_active(emp_id, active_code);
                                    closeOverlay(overlay_id); //this will sadly cause a double ajax_search call since change_active also does ajax_search
                                },
                                error: function (jqXHR, status, error) {
                                    $("#overlay_ajax_message").html(jqXHR.responseText);
                                }
                            });
                        }
                    );
                }
            }
        });
    }

}