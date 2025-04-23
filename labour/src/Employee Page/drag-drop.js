function allowDrop(ev) {
    ev.preventDefault();
}

function drag_employee(ev) {
    // Get HTML element id of the dragged employee
    ev.dataTransfer.setData("dragged_employee_object", ev.target.id);

    // Get the active code of the dragged employee
    var active_code = ev.target.closest('.employee_wrapper').dataset.active_code;

    // Package the job id and employee id into the dataTransfer object
    ev.dataTransfer.setData("job_id", -1);
    ev.dataTransfer.setData("emp_id", ev.target.dataset.emp_id);
    ev.dataTransfer.setData("active_code", active_code);

    // Change colour of wrappers to indicate drop target
    // Make sure that the source wrapper is not affected
    var temp_style = document.createElement('style');
    temp_style.innerHTML = `
        .employee_wrapper {
            background: rgb(0 255 61 / 15%) !important;
        }
        .employee_wrapper[data-active_code="${active_code}"] {
            background: transparent !important;
        }
    `;

    // Append the style element to the head
    document.head.appendChild(temp_style);

    // Add event listener to remove the style element when the drag ends
    ev.target.addEventListener(
        'dragend', 
        function() {
            temp_style.innerHTML = ``;
        }, 
        {once: true} // Delete the event listener after it is called once
    );

}

function drop_employee(ev) {
    ev.preventDefault();

    // Get nearest wrapper active code
    var active_code = ev.target.closest('.employee_wrapper').dataset.active_code;
    var emp_id = ev.dataTransfer.getData("emp_id");

    var origin_active_code = ev.dataTransfer.getData("active_code");

    // Check to make sure that not being dropped on the same wrapper or from the pin bar
    if (origin_active_code == active_code || ev.dataTransfer.getData("pinned")) {
        return;
    }
    // show warning if dragging an archived employee to new status
    if (origin_active_code == -1) {
        var overlay_id = "notification_overlay";
        generate_overlay(overlay_id, "45em", "18em");
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
                <div class="overlay_button reject_hover" onclick="closeOverlay('${overlay_id}')">Cancel</div>
                <div class="overlay_button reject_hover" id="remove_date">Remove Date</div>
                <div class="overlay_button confirm_hover" id="keep_date">Keep Date</div>
            </div>
            <div id="overlay_ajax_message"></div>
            `
        );

        $("#keep_date").click(
            function() {
                change_active(emp_id, active_code);
                closeOverlay(overlay_id);
            }
        );

        $("#remove_date").click(
            function() {
                $.ajax({
                    url: 'reset_archive_date.php',
                    type: 'POST',
                    data: {
                        emp_id: emp_id
                    },
                    success: function(response) {
                        change_active(emp_id, active_code);
                        closeOverlay(overlay_id);
                    },
                    error: function(jqXHR, status, error) {
                        $("#overlay_ajax_message").html(jqXHR.responseText);
                    }
                });
            }
        );

        return;
    }
    // check to see if user is being dropped from active area to inactive or school area
    if (active_code == 0) {
        change_active(emp_id, active_code);        
    } else {
        $.ajax({
            url: "check_emp_job_assignments.php",
            method: "POST",
            data: {
                emp_id: emp_id,
            },
            success: function() {
                change_active(emp_id, active_code);
            },
            error: function(xhr, status, error) {
                var jobs_assigned = JSON.parse(xhr.responseText);
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
                    function() {                                        
                        $.ajax({
                            url: 'mass_remove.php',
                            type: 'POST',
                            data: {
                                emp_id: emp_id
                            },
                            success: function(response) {
                                change_active(emp_id, active_code);    
                                closeOverlay(overlay_id);
                            },
                            error: function(jqXHR, status, error) {
                                $("#overlay_ajax_message").html(jqXHR.responseText);
                            }
                        });
                    }
                );
            }
        });
    }
    
}

document.addEventListener("keydown", function (event) {
    if (event.ctrlKey && event.key === "Control") {
        $("#pin_bar").css("background-color", "rgba(0, 255, 61, 0.15)");
        detect_ctrl = true;
    }
});

document.addEventListener("keyup", function (event) {
    if (event.key === "Control") {
        $("#pin_bar").css("background-color", "");
        detect_ctrl = false;
    }
});

function drag_pin_employee(ev) {
    ev.dataTransfer.setData("dragged_employee_object", ev.target.id);
    ev.dataTransfer.setData("job_id", -1);
    ev.dataTransfer.setData("emp_id", ev.target.dataset.emp_id);
    ev.dataTransfer.setData("active_code", -1);
    ev.dataTransfer.setData("pinned", true);

    // Make unpin_box usable, change elements inside of it
    document.getElementById('unpin_box').style.display = 'block';
    document.getElementById('pin_message').innerText="Unpin";
    $("#pinbutton").attr("src","../img/Interface/X-closed.svg");
    

    // Add event listener to unpin_box to hide it when the drag ends
    ev.target.addEventListener(
        'dragend', 
        function() {
            var pin_message = document.getElementById('pin_message');
            //if there aren't any children, reset pin message
            if($("#pin_bar").children().length < 1){ 
                pin_message.style.color="#00000029";
                pin_message.style.position="Absolute";
                pin_message.innerText="Drag here or Ctrl + Click employees to pin...";
            } else {
                document.getElementById('pin_message').innerText="Pinned:";
            }
            document.getElementById('unpin_box').style.display = 'none';

            // Reset pin button
            $("#pinbutton").attr("src","../img/Interface/pin.svg");
            document.getElementById('pin').style.background="white";
        }, 
        {once: true} // Delete the event listener after it is called once
    );
}

function drop_pin_employee(ev) {
    ev.preventDefault();
    //Set pin message to be Pinned:
    document.getElementById('pin_message').innerText="Pinned:";
    document.getElementById('pin_message').style.color="black";
    document.getElementById('pin_message').style.position="unset";

    var employee_object = ev.dataTransfer.getData("dragged_employee_object");
    
    // Make sure that object is not already in the pin box
    if (ev.dataTransfer.getData("pinned")) {
        return;
    }
    
    // Get the original object
    var originalObject = document.getElementById(employee_object);

    pin_employee(originalObject);
}

function pin_employee(originalObject) {
    // Get wrapper object
    var wrapperObject = document.getElementById('pin_bar');

    // Check that the original object is not already pinned
    if (!originalObject.classList.contains('duplicated')) {    
        // Create a copy of the original object
        var copiedObject = originalObject.cloneNode(true);
        
        // Mark the original object as pinned
        originalObject.classList.add('duplicated');

        // Change copied object drag behaviour
        copiedObject.setAttribute('ondragstart', 'drag_pin_employee(event)');

        // Remove onclick event from copied object
        copiedObject.removeAttribute('onclick');

        // Change copied object id and add data attribute to mark it as pinned
        copiedObject.id = 'pinned_' + copiedObject.id;
        
        // Append the copied object to the target element
        wrapperObject.appendChild(copiedObject);
    }
}

function ctrl_pin_employee(emp_id) {
    //Set pin message to be Pinned:
    document.getElementById('pin_message').innerText="Pinned:";
    document.getElementById('pin_message').style.color="black";
    document.getElementById('pin_message').style.position="unset";

    // Get the original object
    var employee_object = document.getElementById(`employee[${emp_id}]`);

    // Check if the original object is already pinned in the pin box
    var pin_emp = document.getElementById(`pinned_employee[${emp_id}]`);
    if (pin_emp != null) {
        // If it is, remove it from the pin box
        pin_emp.remove();
        employee_object.classList.remove('duplicated');
        if($("#pin_bar").children().length < 1){ 
            pin_message.style.color="#00000029";
            pin_message.style.position="Absolute";
            pin_message.innerText="Drag here or Ctrl + Click employees to pin...";
        }
        return;
    }
    
    pin_employee(employee_object);
}

function unpin_employee(ev) {
    ev.preventDefault();
    
    // Make sure that object is from the pin box
    if (ev.dataTransfer.getData("active_code") != -1) {
        return;
    }
    
    var employee_object = ev.dataTransfer.getData("dragged_employee_object");
    var originalObject = document.getElementById(employee_object);
    originalObject.remove();

    // Undo class change on original object
    var originalObjectID = employee_object.replace('pinned_', '');
    var originalObject = document.getElementById(originalObjectID);
    originalObject.classList.remove('duplicated');
}

function unpin_all() {
    var pin_bar = document.getElementById('pin_bar');

    // Remove all children from pin bar
    while (pin_bar.firstChild) {
        pin_bar.removeChild(pin_bar.firstChild);
    }

    // Remove duplicated class from all employees
    $(".duplicated").removeClass("duplicated");

    // Reset pin message
    var pin_message = document.getElementById('pin_message');
    pin_message.style.color="#00000029";
    pin_message.style.position="Absolute";
    pin_message.innerText="Drag here or Ctrl + Click employees to pin...";
}