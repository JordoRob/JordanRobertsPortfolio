//for showing/hiding the add-job overlay modal
function showAddJobModal() {
    $('.add-job-modal').removeClass('d-none');
    $('.add-job-modal-overlay').removeClass('d-none');
}
function hideAddJobModal() {
    $('.add-job-modal').addClass('d-none');
    $('.add-job-modal-overlay').addClass('d-none');
}

// Generated section header calls this when clicked to toggle section
function minimize(target) {
    $("div[name=section" + target + "]").slideToggle();
    let arrow = $("#section_title_" + target).children(".arrow");
    if (arrow.html() == "▼") {
        arrow.html("►");
    } else {
        arrow.html("▼");
    }
}


// refreshes totals bar
function ajax_totals() {
    $.ajax({
        type: "POST",
        url: "generate_totals.php",
        success: function (data) {
            $(".totals-wrapper").html(data + `<button type='button' id='switch_view' onclick='location.href = "projector_view/projector-view.php";'>Projector View</button>`); // Populate results div with ajax response
        },
        error: function (jqXHR, textStatus, errorThrown) {
            send_message(jqXHR.responseText);
        }
    });
}

// refreshes employee list
function ajax_employees() {
    var search = $("input[name=overlay-search]").val(); //get search value
    if (search == null) {
        search = "";
    }
    var tab = $("#selected-overlay").data("overlay_id");    //get selected tab
    $.ajax({
        type: "POST",
        url: "generate_employee_overlay.php",
        data: {
            search: search,
            tab: tab
        },
        success: function (data) {
            $("#overlay-error").hide(); // Hide error message if ajax call succeeds
            if(data == ""){
                data = "<p class='error'>No Employees Found</p>";
            }
            $("#overlay-content").html(data); // Populate results div with ajax response

            // highlight search term
            let pattern = new RegExp("(" +  search + ")", "gi");
            let itemsToSearch = $("#emp-overlay .employee_details h1");
            itemsToSearch.each(function () {
                let src_str = $(this).text();
                src_str = src_str.replace(pattern, "<mark>$1</mark>");
                src_str = src_str.replace(/(<mark>[^<>]*)((<[^>]+>)+)([^<>]*<\/mark>)/, "$1</mark>$2<mark>$4");
                $(this).html(src_str);
            });
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $("#overlay-error").html(jqXHR.responseText); // Display error message if ajax call fails
            $("#overlay-error").show();

            send_message("An error occurred while getting employees: " + jqXHR.responseText);
        }
    });
}

// refreshes jobs + who's assigned
function ajax_search() {
    // Change dropdown selected class to clicked on element
    if (event != null) {
        e = $(event.target);
        if (e.hasClass("filter-sort")) {
            e.parent().children().removeClass("dropdown-selected");
            e.addClass("dropdown-selected");
        }
    }

    let search_term =  $("input[name=search]").val();

    $.ajax({
        type: "POST",
        url: "generate_job_listing.php",
        data: {
            search: search_term,
            sort: $("a[name=sort].dropdown-selected").data('value'),
            order: $("a[name=order].dropdown-selected").data('value'),
        },
        success: function (data) {
            // Populate results div with ajax response
            if ($("a[name=sort].dropdown-selected").data('value') == "custom") {
                apply_custom_order(data);
            } else {
                $("#job-listings").html(data); 
                makeAllProjectionsEditable();
                $("#order_filter").css("display", "block");
            }

            // highlight search term
            let pattern = new RegExp("(" +  search_term + ")", "gi");
            let itemsToSearch = $("#job-listings .job_info h1, #job-listings .employee_details h1");
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
        }
    });
}

// This adds the on-event listeners and focus effects to the projection numbers.
// This needs to get run every time the jobs are loaded.
function makeAllProjectionsEditable() {
    ////////// stuff for edit projection number
    ////// on edit of a projection number
    //// save old value
    $('.forecast-value').on('focusin', function (e) {
        console.log("Saving value " + $(this).val());
        $(this).data('proj-val', $(this).val());
    });

    $('.forecast-value').on('change', function (e) {
        //get variables from the edit
        let jobid = $(this).attr('jobid');
        let date = $(this).attr('date');
        let count = $(this).val();

        // make sure count is a number
        if (count == "" || count == null || isNaN(count)) {
            // if it's not a number, restore old value
            $(this).val($(this).data('proj-val'));
        } else {
            if ($(this).parent().hasClass("job-overtime") || $(this).parent().hasClass("job-after-end")) {
                if (count > 0) {
                    $(this).parent().removeClass("job-after-end");
                    $(this).parent().addClass("job-overtime");
                    $(this).parent().children("label").css("color", "black");
                } else {
                    $(this).parent().removeClass("job-overtime");
                    $(this).parent().addClass("job-after-end");
                    $(this).parent().children("label").css("color", "grey");
                }
            }
            // Perform ajax call to update the database's projection number
            $.ajax({
                url: 'editProjectionNumber.php',
                type: 'POST',
                data: {
                    jobid: jobid,
                    date: date,
                    count: count
                },
                success: function (response) {
                    send_message(response);
                    ajax_totals();
                },
                error: function (jqXHR, status, error) {
                    send_message(jqXHR.responseText);
                }
            });
        }
    });

    ////// onfocus styling for selecting a projeciton number field (had to make seperate as opposed to :focus in css due to wanting to apply style to parent element)
    $('.forecast-value').on('focus', function (e) {
        $(e.target).parent().addClass("focussed");
    });
    $('.forecast-value').on('blur', function (e) { // on unfocus
        $(e.target).parent().removeClass("focussed");
    });
}

// refreshes the "in school" and "inactive" areas on job page
function job_inactive_update() {
    $.ajax({
        type: "POST",
        url: "./projector_view/grab_inactive.php",
        success: function (data) {
            data = JSON.parse(data);
            $("#inactive-listings").html(data.inactive);
            $("#in-school-listings").html(data.in_school);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            send_message(jqXHR.responseText);
        }
    });
}