/**
 * calls the job-detail-overlay.php file to generate the job details for a given job id and this inserts the generated html into #job-overlay
 *  and then calls generate_graph() to generate the graph
 * @param {integer} job_id The id of the job to get details for
 */
function jobDetails(job_id) {
  $.ajax({
    type: "POST",
    url: "/labour/src/job-details/job-detail-overlay.php",
    datatype: "json",
    data: {
      job_id: job_id
    },
    success: function (data) {
      generate_overlay("job-overlay", '70em', '35em');
      var totaldata = JSON.parse(data);
      $("#job-overlay").html(totaldata); // Populate results div with ajax response

      generate_graph(job_id);

    },
    error: function (jqXHR, textStatus, errorThrown) {
      send_message("An error occurred while getting job details: " + jqXHR.responseText);
    }
  });
}

/**
 * calls the get-job-graph-data.php file to get the graph data for a given job id
 * @param {integer} job_id The id of the job to get the graph data for
 */
function generate_graph(job_id) {
  $.ajax({
    type: "POST",
    url: "/labour/src/job-details/get-job-graph-data.php",
    datatype: "json",
    data: {
      job_id: job_id
    },
    success: function (data) {
      // delete the old graph and make a new one
      $("#graph").remove();
      $("#job-overlay .info-wrapper").append("<canvas id='graph'></canvas>");

      var totaldata = JSON.parse(data);
      var outlookdata = totaldata[1];
      var actualdata = totaldata[2];
      var graphlabels = totaldata[0];
      var startdate = totaldata[3];
      var enddate = totaldata[4];
      var today = totaldata[5];
      outlook_graph(outlookdata, actualdata, graphlabels, startdate, enddate, today);

    },
    error: function (jqXHR, textStatus, errorThrown) {
      send_message("An error occurred while getting job graph: " + jqXHR.responseText);
    }
  });
}

/**
 * 
 * @param {array} outlookdata a 1D array of the projections
 * @param {array} actualdata a 1D array of the actual employee counts
 * @param {array} graphlabels a 1D array of the labels for the graph (dates)
 * @param {String} startdate the start date of the job
 * @param {String} enddate the end date of the job
 * @param {String} today The current date
 */
function outlook_graph(outlookdata, actualdata, graphlabels, startdate, enddate, today) {
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

  var chart = new Chart($("#graph"), {
    data: finaldata,
    type: "line",

    options: {
      aspectRatio: 3,
      pointStyle: false,
      plugins: {
        annotation: {
          annotations: {
            startbox: startbox,
            startline: startline,
            todayline: todayline,
            endbox: endbox,
            endline: endline
          }
        }
      }
    }
  });
}
function enableEditJob() {
  var job_id = $(".info-wrapper").attr("data-job-id");
  // Change to text boxes
  $("#job-title-datafield").replaceWith("<input type='text' id='job-title-datafield' class='info-name' placeholder='Job Title' value='" + escapeHTML($("#job-title-datafield").text()) + "' pattern='.{0,255}' title='Must be less than 255 characters' required/>");
  $("#job-manager-datafield").replaceWith("<input type='text' id='job-manager-datafield' placeholder='Manager Name' class='info-subheading' value='" + escapeHTML($("#job-manager-datafield").text()) + "' pattern='.{0,255}' title='Must be less than 255 characters' />");

  if (navigator.userAgent.toLowerCase().indexOf('firefox') > -1 || /^((?!chrome|android).)*safari/i.test(navigator.userAgent)) {
    // if firefox or safari, do a date field instead of month
    let startdate = $("#job-startdate-datafield").text().substring(0, 7);
    let enddate = $("#job-enddate-datafield").text().substring(0, 7);
    $("#job-startdate-datafield").replaceWith("<input type='date' id='job-startdate-datafield' value='" + startdate + "-01' />");
    $("#job-enddate-datafield").replaceWith("<input type='date' id='job-enddate-datafield' value='" + enddate + "-01' />");
  } else {
    $("#job-startdate-datafield").replaceWith("<input type='month' id='job-startdate-datafield' value='" + $("#job-startdate-datafield").text() + "' />");
    $("#job-enddate-datafield").replaceWith("<input type='month' id='job-enddate-datafield' value='" + $("#job-enddate-datafield").text() + "' />");
  }
  $("#job-address-datafield").replaceWith("<input type='text' id='job-address-datafield' value='" + escapeHTML($("#job-address-datafield").text()) + "' pattern='.{0,255}' title='Must be less than 255 characters' />");
  $("#job-notes-datafield").removeAttr("disabled");
  $('#overlay_delete').css("visibility", "visible");
  $('#overlay_archive').css("visibility", "visible");
  // add validation stuff
  let jobTitle = document.getElementById("job-title-datafield");
  let jobTitleErrorMsg = document.getElementById("job-title-error");
  let jobManager = document.getElementById("job-manager-datafield");
  let jobManagerErrorMsg = document.getElementById("job-manager-error");
  let jobAddress = document.getElementById("job-address-datafield");
  let jobAddressErrorMsg = document.getElementById("job-address-error");
  let jobEndDate = document.getElementById("job-enddate-datafield");
  let jobEndDateErrorMsg = document.getElementById("job-enddate-error");
  let jobStartDate = document.getElementById("job-startdate-datafield");
  let jobStartDateErrorMsg = document.getElementById("job-startdate-error");

  //// Key listeners for their corresponding fields
  jobTitle.addEventListener('keyup', function (event) {
    validateJobTitle(jobTitle, jobTitleErrorMsg);
  });
  jobManager.addEventListener('keyup', function (event) {
    validateJobManager(jobManager, jobManagerErrorMsg);
  });
  jobAddress.addEventListener('keyup', function (event) {
    validateJobAddress(jobAddress, jobAddressErrorMsg);

  });
  jobEndDate.addEventListener('change', function (event) {
    validateJobEndDate(jobStartDate, jobEndDate, jobEndDateErrorMsg);
    firefoxMonth(jobEndDate);
  });
  jobStartDate.addEventListener('change', function (event) {
    validateJobStartDate(jobStartDate, jobStartDateErrorMsg, jobEndDate, jobEndDateErrorMsg);
    firefoxMonth(jobStartDate);
  });

  // Change image
  $(`#overlay_edit_job_${job_id}`).attr("src", "/labour/src/img/save.svg");
  $(`#overlay_edit_job_${job_id}`).attr("onclick", "saveEditJob()");
  $(`#overlay_edit_job_${job_id}`).attr("title", "Save Job");
  // Fade save button background color every 3 seconds
  var flip = false;
  fade_job = setInterval(
    function () {
      if (flip) {
        $(`#overlay_edit_job_${job_id}`).css("background-color", "");
        flip = false;
      } else {
        $(`#overlay_edit_job_${job_id}`).css("background-color", "#d23127");
        flip = true;
      }
    },
    1000
  );
}
function saveEditJob() {
  clearInterval(fade_job);
  var job_id = $(".info-wrapper").attr("data-job-id");
  $(`#overlay_edit_job_${job_id}`).attr("src", "/labour/src/img/loading.svg");
  $(`#overlay_edit_job_${job_id}`).attr("title", "Saving...");
  $(`#overlay_edit_job_${job_id}`).attr("onclick", "");
  $.ajax({
    type: "POST",
    url: "/labour/src/job-details/update_job.php",
    datatype: 'json',
    data: {
      job_id: job_id,
      manager: $("#job-manager-datafield").val(),
      startdate: $("#job-startdate-datafield").val(),
      enddate: $("#job-enddate-datafield").val(),
      address: $("#job-address-datafield").val(),
      title: $("#job-title-datafield").val(),
      notes: $("#job-notes-datafield").val()
    },
    success: function (data) {
      if (JSON.parse(data)[0]) {
        //reset all fields validation
        var otherFields = document.querySelectorAll("input");
        otherFields.forEach(function (field) {
          field.classList.remove('is-valid');
          field.classList.remove('is-invalid');
        });

        $(`#overlay_edit_job_${job_id}`).attr("src", "/labour/src/img/success.svg");
        $(`#overlay_edit_job_${job_id}`).css("background-color", "green");
        generate_graph(job_id);
        // Set timer to change back to edit button
        setTimeout(
          function () {
            clearInterval(fade_job);
            $(`#overlay_edit_job_${job_id}`).attr("src", "/labour/src/img/edit.svg");
            $(`#overlay_edit_job_${job_id}`).attr("onclick", "enableEditJob()");
            $(`#overlay_edit_job_${job_id}`).attr("title", "Edit Job Details");
            $(`#overlay_edit_job_${job_id}`).css("background-color", "");
            //jobDetails(job_id);
          },
          3000
        );

        // Change back to text
        $("#job-manager-datafield").replaceWith("<span id='job-manager-datafield' class='info-subheading'>" + escapeHTML($("#job-manager-datafield").val()) + "</span>");
        $("#job-startdate-datafield").replaceWith("<span id='job-startdate-datafield'>" + escapeHTML($("#job-startdate-datafield").val()) + "</span>");
        $("#job-enddate-datafield").replaceWith("<span id='job-enddate-datafield'>" + escapeHTML($("#job-enddate-datafield").val()) + "</span>");
        $("#job-address-datafield").replaceWith("<span id='job-address-datafield'>" + escapeHTML($("#job-address-datafield").val()) + "</span>");
        $("#job-title-datafield").replaceWith("<span class='info-name' id='job-title-datafield'>" + escapeHTML($("#job-title-datafield").val()) + "</span>");

        $("#job-notes-datafield").attr("disabled", "disabled");
        $('#overlay_delete').css("visibility", "hidden");
        $('#overlay_archive').css("visibility", "hidden");
      } else {
        $(`#overlay_edit_job_${job_id}`).attr("src", "/labour/src/img/error.svg");
        $(`#overlay_edit_job_${job_id}`).css("background-color", "red");
        // Set timer to change back to edit button
        setTimeout(
          function () {
            $(`#overlay_edit_job_${job_id}`).attr("src", "/labour/src/img/save.svg");
            $(`#overlay_edit_job_${job_id}`).attr("onclick", "saveEditJob()");
          },
          3000
        );

        send_message(JSON.parse(data)[1], true);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      $(`#overlay_edit_job_${job_id}`).attr("src", "/labour/src/img/error.svg");
      $(`#overlay_edit_job_${job_id}`).css("background-color", "red");
      // Set timer to change back to edit button
      setTimeout(
        function () {
          $(`#overlay_edit_job_${job_id}`).attr("src", "/labour/src/img/save.svg");
          $(`#overlay_edit_job_${job_id}`).attr("onclick", "saveEditJob()");
        },
        3000
      );
      // error message from server (bad input etc.)
      send_message(JSON.parse(jqXHR.responseText)[1], true);
    }
  });
}
function firefoxMonth(enteredDate) {
  if ($(enteredDate).val().length > 7) {
    let adjustedDate = $(enteredDate).val().substring(0, 7) + "-01";
    $(enteredDate).val(adjustedDate);
  }
}

function archiveJob(job_id, job_title) {
  $.ajax({
    url: "/labour/src/job-details/check_if_job_has_no_emps.php",
    method: "POST",
    data: {
      job_id: job_id
    },
    success: function () {
      // if the check for if job has employees passes (i.e. job has no emps), show a warning anyway
      var overlay_id = "archive_job_warning_overlay";

      // Generate confimation overlay
      generate_overlay(overlay_id, "45em", "12em");

      $(`#${overlay_id}`).html(
        `
              <h1 class="info-name">Caution!</h1>
              <span id="overlay_separator_line"></span>
              <p class="overlay_notification_message">Are you sure you want to archive ${decodeURIComponent(job_title)}?</p>
              <br>
              <div class="overlay_notification_buttons">
                  <div class="overlay_button reject_hover" onclick="closeOverlay('${overlay_id}')">Cancel</div>
                  <div class="overlay_button confirm_hover" id="confirm_remove">Confirm</div>
              </div>
              <div id="overlay_ajax_message"></div>
              `
      );

      // Add event listener to confirm button
      $("#confirm_remove").click(
        // finally archive the job (this is in two spots btw, scroll down)
        function () {
          $.ajax({
            url: '/labour/src/job-details/archive_job.php',
            type: 'POST',
            data: {
              job_id: job_id
            },
            success: function (response) {
              send_message(`Successfully archived ${decodeURIComponent(job_title)}`);
              closeOverlay(overlay_id);
              closeOverlay("job-overlay");
            },
            error: function (jqXHR, status, error) {
              send_message(`Error archiving ${decodeURIComponent(job_title)}: ` + jqXHR.responseText, true);
            }
          });
        }
      );
    },
    error: function (xhr, status, error) {
      // if the check for if job has employees fails, then the response text will be the list of employees assigned to the job
      var emps_assigned = JSON.parse(xhr.responseText);
      var overlay_id = "archive_job_warning_overlay";

      // Generate confimation overlay
      generate_overlay(overlay_id, "45em", "15em");

      $(`#${overlay_id}`).html(
        `
              <h1 class="info-name">Caution!</h1>
              <span id="overlay_separator_line"></span>
              <p class="overlay_notification_message">Are you sure you want to archive ${decodeURIComponent(job_title)}?</p>
              <p class="overlay_notification_message">Archiving this job will unassign the following employees from this job:</p>
              <div class="overlay_notification_list"></div>
              `
      );
      for (var i = 0; i < emps_assigned.name.length; i++) {
        $(".overlay_notification_list").append(
          `
                  <p> ∙ ${emps_assigned.name[i]}</p>
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
        // finally archive the job (this is in two spots btw, scroll up)
        function () {
          $.ajax({
            url: '/labour/src/job-details/archive_job.php',
            type: 'POST',
            data: {
              job_id: job_id
            },
            success: function (response) {
              send_message(`Successfully archived ${decodeURIComponent(job_title)}`);
              closeOverlay(overlay_id);
              closeOverlay("job-overlay");
            },
            error: function (jqXHR, status, error) {
              send_message(`Error archiving ${decodeURIComponent(job_title)}: ` + jqXHR.responseText, true);
            }
          });
        }
      );
    }
  });
}

function unarchiveJob(job_id, job_title) {
  $.ajax({
    url: '/labour/src/job-details/unarchive_job.php',
    type: 'POST',
    data: {
      job_id: job_id
    },
    success: function (response) {
      send_message(`Successfully unarchived ${decodeURIComponent(job_title)}`);
      closeOverlay("job-overlay");
    },
    error: function (jqXHR, status, error) {
      send_message(`Error unarchiving ${decodeURIComponent(job_title)}: ` + jqXHR.responseText, true);
    }
  });
}

function deleteJob(job_id, job_title) {
  // before doing anything, check if any employees are assigned
  $.ajax({
    url: "/labour/src/job-details/check_if_job_has_no_emps.php",
    method: "POST",
    data: {
      job_id: job_id
    },
    success: function () {
      // if there are no employees assigned, show the next warning for actually deleting the job
      // Generate confimation overlay
      var overlay_id = "delete_job_overlay";
      generate_overlay(overlay_id, "45em", "13em");

      $(`#${overlay_id}`).html(
        `
      <h1 class="info-name">Caution!</h1>
      <span id="overlay_separator_line"></span>
      <p class="overlay_notification_message">
          Are you sure you want to delete this job?
      </p>
      <p class="overlay_notification_message important_message">
          Deleting is permanent and removes the history of employees working on this job, unlike archiving.
      </p>
      <div class="overlay_notification_buttons">
          <div class="overlay_button reject_hover" onclick="closeOverlay('${overlay_id}')">Cancel</div>
          <div class="overlay_button confirm_hover" id="confirm_delete">Confirm</div>
      </div>
      <div id="overlay_ajax_message"></div>
      `
      );

      // Add event listener to confirm button that sends a request to delete_job.php to delete the job
      $("#confirm_delete").click(
        function () {
          $.ajax({
            type: "POST",
            url: "/labour/src/job-details/delete_job.php",
            data: {
              job_id: job_id
            },
            success: function (data) {
              closeOverlay(overlay_id);
              closeOverlay("job-overlay");
              send_message(`Successfully deleted ${decodeURIComponent(job_title)}`);
            },
            error: function (jqXHR, textStatus, errorThrown) {
              send_message(`Error deleting ${decodeURIComponent(job_title)}: ` + jqXHR.responseText, true);
              console.log(`Error deleting ${decodeURIComponent(job_title)}: ` + jqXHR.responseText);
            }
          });
        }
      );
    },
    error: function (xhr, status, error) {
      // if there are employees assigned, show a warning
      send_message(`Error deleting ${decodeURIComponent(job_title)}: Job still has employees assigned`, true);
    }
  });
}