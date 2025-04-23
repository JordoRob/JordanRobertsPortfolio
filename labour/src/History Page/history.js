
// Generated section header calls this when clicked to toggle section
function minimize(target) {
    $("div[name=section" + target + "]").toggle("fast");
    let arrow = $("#section_title_" + target).children(".arrow");
    if (arrow.html() == "▼") {
        arrow.html("►");
    } else {
        arrow.html("▼");
    }
}

// will load the job listing history page with all the jobs (and their graphs).
// This is called when the page is first loaded
function ajax_search() {
    // Change dropdown selected class to clicked on element
    if (event != null) {
        e = $(event.target);
        if (e.hasClass("filter-sort")) {
            e.parent().children().removeClass("dropdown-selected");
            e.addClass("dropdown-selected");
        }
    }

    // put up loading screen
    $(".main_content").append('<div id="history-loading-block"><img src="/labour/src/img/loading.svg"></div>');

    let search_term = $("input[name=search]").val();

    $.ajax({
        type: "POST",
        url: "generate_job_listing_history.php",
        data: {
            search: search_term,
            sort: $("a[name=sort_history].dropdown-selected").data('value'),
            order: $("a[name=order_history].dropdown-selected").data('value'),
            showAllJobs: $("#archived-all-toggler").prop("checked"),
            start_date: $("#start-date-input").val(),
            end_date: $("#end-date-input").val(),
            page: $("#page-input").val(),
            per_page: $("#per-page-input").val()
        },
        success: function (data) {
            // Populate results div with ajax response
            $("#job-listings").html(data);
            $("#order_filter").css("display", "block");

            // for every job, load its graph (not used anymore, genereated in php now)
            // $(".job_listing").each(function () {
            //     //generate_history_graph($(this).data("job_id"));
            // });

            // highlight search term
            let pattern = new RegExp("(" + search_term + ")", "gi");
            let itemsToSearch = $(".job_info h1");
            itemsToSearch.each(function () {
                let src_str = $(this).text();
                src_str = src_str.replace(pattern, "<mark>$1</mark>");
                src_str = src_str.replace(/(<mark>[^<>]*)((<[^>]+>)+)([^<>]*<\/mark>)/, "$1</mark>$2<mark>$4");
                $(this).html(src_str);
            });

            // remove loading screen
            $("#history-loading-block").remove();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            send_message(jqXHR.responseText);
        }
    });
}

// TODO: ADD start range and end range
/**
 * NO LONGER NEEDED. generate_job_listing_history.php now puts the data right into the html
 * 
 * calls the get-job-graph-data.php file to get the graph data for a given job id
 * @param {integer} job_id The id of the job to get the graph data for
 */
// function generate_history_graph(job_id) {
//     $.ajax({
//         type: "POST",
//         url: "/labour/src/History Page/get-job-graph-data-history.php",
//         datatype: "json",
//         data: {
//             job_id: job_id
//         },
//         success: function (data) {

//             var totaldata = JSON.parse(data);
//             var outlookdata = totaldata[1];
//             var actualdata = totaldata[2];
//             var graphlabels = totaldata[0];
//             var startdate = totaldata[3];
//             var enddate = totaldata[4];
//             var today = totaldata[5];
//             outlook_graph_history(outlookdata, actualdata, graphlabels, startdate, enddate, today, job_id);

//         },
//         error: function (jqXHR, textStatus, errorThrown) {
//             send_message("An error occurred while getting job graph: " + jqXHR.responseText);
//         }
//     });
// }

/**
 * Generates a graph for the given job_id
 * @param {array} outlookdata a 1D array of the projections
 * @param {array} actualdata a 1D array of the actual employee counts
 * @param {array} graphlabels a 1D array of the labels for the graph (dates)
 * @param {String} startdate the start date of the job
 * @param {String} enddate the end date of the job
 * @param {String} today The current date
 * @param {integer} job_id The id of the job to print the graph for
 * @param {boolean} isTopJob Whether or not the job is a top job (used for showing legend if true)
 * @param {boolean} isBottomJob Whether or not the job is a bottom job (used for showing labels if true)
 */
function outlook_graph_history(outlookdata, actualdata, graphlabels, startdate, enddate, today, job_id, isTopJob = false, isBottomJob = false) {
    var startline = { display: false };//incase these arent included in the timeframe
    var startbox = { display: false };
    var endline = { display: false };
    var endbox = { display: false };
    var todayline = { display: false };

    if (startdate != null) {
        var startbox = {
            type: 'box',
            xMin: 0,
            xMax: startdate,
            backgroundColor: 'rgba(0, 0, 0, 0.25)'
        };
        var startline = {
            type: 'line',
            xMin: startdate,
            xMax: startdate,
            borderColor: 'black',
            borderWidth: 2,
            borderDash: [5, 5],
            borderShadowColor: 'black',
            label: {
                content: "Start Date",
                display: true,
                position: 'end',
            }
        };
    }

    if (enddate != null) {
        endline = {
            type: 'line',
            xMin: enddate,
            xMax: enddate,
            borderColor: 'black',
            borderWidth: 2,
            borderDash: [5, 5],
            borderShadowColor: 'black',
            label: {
                content: "End Date",
                display: true,
                position: 'end'
            }
        };
        endbox = {
            type: 'box',
            xMin: enddate,
            backgroundColor: 'rgba(0, 0, 0, 0.25)'
        };
    }

    if (today != null) {
        var todayline = {
            type: 'line',
            xMin: today,
            xMax: today,
            borderColor: 'black',
            borderWidth: 2,
            borderDash: [5, 5],
            borderShadowColor: 'black',
            label: {
                content: "Today",
                display: true,
                position: 'end',
            }
        };
    }
    const finaldata = {
        labels: graphlabels,
        datasets: [{
            label: "Outlook",
            data: outlookdata,
        },
        {
            label: "Actual",
            data: actualdata,
        }],
    };

    var chart = new Chart($(`#graph-${job_id}`), {
        data: finaldata,
        type: "line",


        options: {
            maintainAspectRatio: false, // set it to fill container (and also auto-size)
            pointStyle: false,
            scales: {
                x: {
                    display: isBottomJob, // This will hide the x-axis labels if the job is not the bottom job
                    ticks: {
                        maxRotation: 90, // Set the maximum rotation angle for the x-axis labels
                        minRotation: 90 // Set the minimum rotation angle for the x-axis labels
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        callback: function (value, index, values) {
                            return value.toString().padStart(2, '0'); // Format the value to have 2 digits with leading zeros
                        }
                    }
                }
            },
            plugins: {
                annotation: {
                    annotations: {
                        startbox: startbox,
                        startline: startline,
                        todayline: todayline,
                        endbox: endbox,
                        endline: endline
                    }
                },
                legend: { // for hiding the legend
                    display: isTopJob, // This will hide the legend if the job is not the top job

                }
            }
        }
    });
}

