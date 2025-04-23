function projector_search() {
    $.ajax({
        type: "POST",
        url: "../generate_job_listing.php",
        data: {
            search: "",
            sort: "custom",
            order: "asc",
        },
        success: function (data) {                
            projector_custom_order(data);  
        },
        error: function (jqXHR, textStatus, errorThrown) {
            send_message(jqXHR.responseText);
        }
    });
}

function projector_custom_order(job_listings_html) {
    // Create a jQuery object from the raw HTML string
    var job_listings = $(job_listings_html);
    
    // Create an object to store the job listings as key-value pairs
    var job_listings_obj = {};
    job_listings.each(function() {
        var job_id = $(this).data('job_id');
        job_listings_obj[job_id] = $(this);
    });

    $.ajax({
        url: '../custom_order/get_custom_order.php',
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

            $(".employee").find("h1").each(function () {
                if (this.scrollWidth > this.clientWidth) {
                    let name=$(this).text();
                    $(this).addClass("long-name");
                    $(this).html("&nbsp");
                    $(this).attr("data-text",name);
                }
            }); 
        },
        error: function(jqXHR, status, error) {
            send_message(jqXHR.responseText + ": " + error);
        }
    });

    $("#order_filter").css("display", "none");
}

function projector_totals() {
    $.ajax({
        type: "POST",
        url: "../generate_totals.php",
        success: function (data) {
            $(".totals-wrapper").html(data + `<button type='button' id='switch_view' onclick='location.href = "../job-view.php";'>Exit</button>`); // Populate results div with ajax response
        },
        error: function (jqXHR, textStatus, errorThrown) {
            send_message(jqXHR.responseText);
        }
    });
}

function inactive_update() {
    $.ajax({
        type: "POST",
        url: "grab_inactive.php",
        success: function (data) {
            data = JSON.parse(data);
            $("#inactive-listings").html(data.inactive);
            $("#in-school-listings").html(data.in_school);
            let padding=$('footer').height()+$("#inactive-employees").height();
            $("#job-listings").css("padding-bottom",padding);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            send_message(jqXHR.responseText);
        }
    });
}

const COUNTDOWN_SECS = 10;
var last_check = Math.round(Date.now() / 1000);
function projector_refresh() {
    setInterval(function () {
        $.ajax({
            url: "/labour/src/refresh/check-update.php",
            type: 'POST',
            data: {
                page: "job",
                last_check: last_check  //when to compare against
            },
            dataType: "json",
            success: function (data) {
                if (data[0]) {
                    last_check = Math.round(Date.now() / 1000);
                    projector_search();
                    projector_totals();
                    inactive_update();
                }
            }
        });
    }, COUNTDOWN_SECS * 1000);
}