function batch_assign() {
    // Get all pinned employees from the pin bar
    var employee_ids = [];
    employee_ids = $("#pin_bar").children().map(function() {
        return $(this).attr("data-emp_id");
    }).get();

    var overlay_id = 'batch_assign_overlay';
    generate_overlay(overlay_id);
    $("#batch_assign_overlay").append(
        `        
        <img src="/labour/src/img/close.svg" class="close_overlay" onclick="closeOverlay('${overlay_id}')">
        <div class="info-name">Batch Assign</div>
        <span id="overlay_separator_line"></span>
        <p>Note: If inactive employees are included, they will be moved to active status. Also, this does not unassign employees already working on a job.</p>
        <div id="overlay_content">
            <div id="overlay_employees">
                <div id="overlay_responses"></div>
            </div>                
        </div>        
        `
    );

    $('#batch_assign_overlay').css({height: ''});

    $(".second_div").css({
        'width': ($(".first_div").width() + 'px')
    });

    $("#pin_bar")
    .clone()
    .attr("id", "pin_bar_overlay")
    .removeAttr("data-job_id")
    .removeAttr("ondrop")
    .removeAttr("ondragover")
    .prependTo("#overlay_employees");

    // For each pinned employee in the cloned pin bar, remove the drag and drop functionality
    $("#pin_bar_overlay").children().each(function() {
        $(this).removeAttr("draggable");
    }); 

    $("#overlay_content").append(
        `
        <div id="arrows">▽ ▽ ▽ ▽ ▽ ▽ ▽ ▽ ▽ ▽</div>
        <div id="batch_assign_buttons">
            <select id="batch_assign_select"></select>
            <span id="batch_assign_apply" class="overlay_button confirm_hover" onclick="batch_assign_submit()">Assign</span>
        </div>
        `
    )

    // Add centered empty message if no pinned employees and disable select
    if ($("#pin_bar_overlay").children().length == 0) {
        $("#batch_assign_select").prop("disabled", true);
        $(".overlay_button").attr("onclick", "");
        $(".overlay_button").css("color", "#6e6e6e");
        $(".overlay_button").removeClass("confirm_hover");
        $("#pin_bar_overlay").append(
            `
            <div id="overlay_empty_message">No pinned employees</div>
            `
        );
        $("#pin_bar_overlay").css({
            'justify-content': 'center',
            'align-content': 'center'
        });
    } else {
        // Create confirmation overlay for batch assign
        $.ajax({
            type: "POST",
            url: "/labour/src/Employee Page/Pin Bar Functions/get-batch-add-overlay.php",
            success: function(data) {
                // decode json
                data = JSON.parse(data);
                let ids = data.map(item => item.id);
                let titles = data.map(item => item.title);
                for (var i = 0; i < ids.length; i++) {
                    $("#batch_assign_select").append(
                        `
                        <option value="${ids[i]}">${titles[i]}</option>
                        `
                    )
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                send_message(jqXHR.responseText);
            }
        });
    }
}
    
function batch_assign_submit() {
    var job_id = $("#batch_assign_select").val();
    var employee_ids = [];
    employee_ids = $("#pin_bar").children().map(function() {
        return $(this).attr("data-emp_id");
    }).get();

    $("#pin_bar_overlay").css("width", "50%");
    $("#overlay_responses").show();
    
    for (var i = 0; i < employee_ids.length; i++) {
        $.ajax({
            type: "POST",
            url: "/labour/src/job-view/move_employee.php",
            data: {
                start_job_id: -1,
                end_job_id: job_id,
                emp_id: employee_ids[i]
            },
            success: function(response) {
                $("#overlay_responses").append(
                    `
                    <p>${escapeHTML(response)}</p>
                    `
                );
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $("#overlay_responses").append(
                    `
                    <p>${escapeHTML(jqXHR.responseText)}</p>
                    `
                );
            }
        });
    };
}