var show_edit_after_load = false;
function show_custom_edit() {
    // Switch to custom order filter
    var custom_button = $("#custom_order_filter");
    custom_button.parent().children().removeClass("dropdown-selected");
    custom_button.addClass("dropdown-selected");

    // Clear the search bar - Otherwise custom order editing will only show the jobs that match the search
    $("input[name='search']").val("");

    show_edit_after_load = true;
    ajax_search();
}

function continue_show_custom_edit() {
    var overlay_id = "custom-order";
    generate_overlay(overlay_id, "30em", "40em");
    $(`#${overlay_id}`).html(
        `
        <h1 class="info-name">Edit Custom Order</h1>
        <span id="overlay_separator_line"></span>
        <p>Drag and drop the jobs to change the order.</p>

        <div id="custom_order_edit_container"></div>

        <div class="overlay_notification_buttons">
            <div class="overlay_button reject_hover" onclick="closeOverlay('${overlay_id}')">Cancel</div>
            <div class="overlay_button confirm_hover" id="save_projector_order">Set Projector</div>
            <div class="overlay_button confirm_hover" id="save_custom_order">Save</div>
        </div>
        <div id="overlay_ajax_message"></div>
        `
    );

    // Get job titles and ids from #job-listings
    let jobs = [];
    $('#job-listings .job_listing').each(function() {
        let title = $(this).find('.job_info > h1');
        
        jobs.push({
            title: title.text(),
            id: $(this).data('job_id')
        });
    });
    
    $.each(jobs, function(index, job) {
        $("#custom_order_edit_container").append(
        `
            <div 
                class='custom_order_job_listing' 
                data-job_id='${job.id}'}' 
                data-index='${index}'
                draggable='true' ondragstart='custom_order_drag_start(event)'
                ondrop='custom_order_drop(event)' ondragover='custom_order_drag_over(event)'
            >
                <h1>${job.title}</h1>
            </div>
        `);
    });


    $("#save_custom_order").click(function() {
        send_save_order_request(0, overlay_id);
    });
    $("#save_projector_order").click(function() {
        send_save_order_request(1, overlay_id);
    });
    
}

/**
 * Sends a request to save the custom order
 * @param {*} type - 0 for custom order, 1 for projector order
 * @param {*} overlay_id - The id of the overlay to close after the request is sent
 */
function send_save_order_request(type, overlay_id) {
    var jobIds = $('.custom_order_job_listing').map(function() {
        return $(this).attr('data-job_id'); // Using attr rather than data to get value as string
    }).get();

    $.ajax({
        url: './custom_order/save_custom_order.php',
        type: 'POST',
        data: {
            custom_order: JSON.stringify(jobIds),
            type: type
        },
        success: function(response) {
            send_message(response);
            closeOverlay(overlay_id);
            ajax_search();
        },
        error: function(jqXHR, status, error) {
            $("#overlay_ajax_message").html(jqXHR.responseText);
        }
    });
}

function custom_order_drag_start(ev) {
    // Always get job index from the nearest .custom_order_job_listing
    var index = ev.target.closest(".custom_order_job_listing").dataset.index;
    ev.dataTransfer.setData("dragged_job_index", index);
}

function custom_order_drop(ev) {
    var drop_on_index = ev.target.closest(".custom_order_job_listing").dataset.index;
    var dragged_index = ev.dataTransfer.getData("dragged_job_index");

    // Remove color from the drop zone
    ev.target.closest(".custom_order_job_listing").style.backgroundColor = "transparent";

    // Move the job (shifts all the other jobs below/above where the job was moved as opposed to swapping them)
    if (drop_on_index < dragged_index) {
        $(".custom_order_job_listing[data-index='" + drop_on_index + "']").before($(".custom_order_job_listing[data-index='" + dragged_index + "']"));
    } else if (drop_on_index > dragged_index) {
        $(".custom_order_job_listing[data-index='" + drop_on_index + "']").after($(".custom_order_job_listing[data-index='" + dragged_index + "']"));
    }

    // Update the index
    $(".custom_order_job_listing").each(function(index) {
        $(this).attr("data-index", index);
    });
}

function custom_order_drag_over(ev) {
    ev.preventDefault();

    var dropelement = ev.target.closest(".custom_order_job_listing");
    
    // Change color of the drop zone
    dropelement.style.backgroundColor = "#d73333";

    // Remove the color once cursor leaves the drop zone or drag ends
    function handleDragLeave() {
        dropelement.style.backgroundColor = "transparent";
        dropelement.removeEventListener("dragleave", handleDragLeave);
    }
    
    dropelement.addEventListener("dragleave", handleDragLeave);
}

function apply_custom_order(job_listings_html) {
    // Create a jQuery object from the raw HTML string
    var job_listings = $(job_listings_html);
    
    // Create an object to store the job listings as key-value pairs
    var job_listings_obj = {};
    job_listings.each(function() {
        var job_id = $(this).data('job_id');
        job_listings_obj[job_id] = $(this);
    });

    $.ajax({
        url: './custom_order/get_custom_order.php',
        type: 'POST',
        success: function(response) {
            // response contains array of job ids in the custom order
            var job_ids = JSON.parse(response);

            // Get the job listings from the object in the order of the job ids
            var ordered_job_listings = [];
            for (var i = 0; i < job_ids.length; i++) {
                // Find the job listing with the job id and push it to the ordered_job_listings array
                ordered_job_listings.push(job_listings_obj[job_ids[i]]);
                // Remove the job listing from the job_listings_obj object
                delete job_listings_obj[job_ids[i]];
            }

            // If there are any job listings left in the job_listings_obj object, append them to the end of the ordered_job_listings array
            for (var key in job_listings_obj) {
                ordered_job_listings.push(job_listings_obj[key]);
            }

            // Append all the job listings to the $("#job-listings") div at once
            $("#job-listings").html(ordered_job_listings);
            if (show_edit_after_load) continue_show_custom_edit();
            show_edit_after_load = false;

            makeAllProjectionsEditable();
        },
        error: function(jqXHR, status, error) {
            console.log(error);
            show_edit_after_load = false;
        }
    });

    $("#order_filter").css("display", "none");
}


