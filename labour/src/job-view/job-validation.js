// on document load
$(function () {
    ////////// stuff for add job
    //////// check if using firefox or safari and replace the month input type with date (since month isn't supported on those browsers)
    if (navigator.userAgent.toLowerCase().indexOf('firefox') > -1 || /^((?!chrome|android).)*safari/i.test(navigator.userAgent)) {
        document.querySelectorAll('input[type=month]').forEach(function (field) {
            field.setAttribute('type', 'date');
        });
    }

    //////// Set default value of create job start date to now
    let jobStartDate = document.getElementById("job-start");
    jobStartDate.valueAsDate = new Date();

    //////// Add job form validation
    let jobform = document.getElementById("job-form");
    let jobTitle = document.getElementById("job-title");
    let jobTitleErrorMsg = document.getElementById("job-title-error");
    let jobManager = document.getElementById("job-manager");
    let jobManagerErrorMsg = document.getElementById("job-manager-error");
    let jobAddress = document.getElementById("job-address");
    let jobAddressErrorMsg = document.getElementById("job-address-error");
    let jobEndDate = document.getElementById("job-end");
    let jobEndDateErrorMsg = document.getElementById("job-end-error");
    let jobStartDateErrorMsg = document.getElementById("job-start-error");

    function clearAddJobModal(event) {
        // reset job start validation (it's special because it has a default value)
        jobStartDate.classList.remove('is-valid');
        jobStartDate.classList.remove('is-invalid');
        jobStartDate.valueAsDate = new Date(); //reset to today
        //reset other fields
        var otherFields = document.querySelectorAll("input:not(#job-start)");
        otherFields.forEach(function (field) {
            field.value = field.defaultValue;
            field.classList.remove('is-valid');
            field.classList.remove('is-invalid');
        });
        hideAddJobModal();
    }
        
    jobform.onsubmit = function (e) {
        e.preventDefault(); // stop form from submitting to do validation first
        let success = true;

        if (!validateJobTitle(jobTitle, jobTitleErrorMsg)) { success = false; }
        if (!validateJobManager(jobManager, jobManagerErrorMsg)) { success = false; }
        if (!validateJobAddress(jobAddress, jobAddressErrorMsg)) { success = false; }
        if (!validateJobStartDate(jobStartDate, jobStartDateErrorMsg, jobEndDate, jobEndDateErrorMsg)) { success = false; }
        if (!validateJobEndDate(jobStartDate, jobEndDate, jobEndDateErrorMsg)) { success = false; }

        if (success) {
            $.ajax({
                type: "POST",
                url: "addJob.php",
                data: $("#job-form").serialize(),
                success: function (data) {
                    // Close the add job modal
                    clearAddJobModal();
                    jobDetails(data)
                },
                error: function (data, textStatus, jqXHR) {
                    // Display error message
                    send_message(data.responseText, true);
                }
            });
        }
    };
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
    });
    jobStartDate.addEventListener('change', function (event) {
        validateJobStartDate(jobStartDate, jobStartDateErrorMsg, jobEndDate, jobEndDateErrorMsg);
    });
    //// On add job cancel (which clears the form), remove validation
    document.getElementById('job-reset').addEventListener('mouseup', clearAddJobModal);
});




/**
 * Validates the job title. Requires jobTitle and jobTitleErrorMsg params 
 * to prevent needing to getElementById on every form validation and keypress
 * (plus make it more maintainable by having only one getElementById outside the function)
 * @param {HTMLElement} jobTitle
 * @param {HTMLElement} jobTitleErrorMsg 
 * @returns {Boolean} true if successful validation, false otherwise
 */
function validateJobTitle(jobTitle, jobTitleErrorMsg) {
    if (jobTitle.validity.valueMissing) {
        jobTitle.classList.remove('is-valid');
        jobTitle.classList.add('is-invalid');
        jobTitleErrorMsg.innerHTML = 'Job Title Must Be Filled';
        return false;
    } else if (jobTitle.validity.patternMismatch) {
        jobTitle.classList.remove('is-valid');
        jobTitle.classList.add('is-invalid');
        jobTitleErrorMsg.innerHTML = 'Must be less than 255 characters';
        return false;
    } else {
        jobTitle.classList.add('is-valid');
        jobTitle.classList.remove('is-invalid');
        return true;
    }
}

/**
 * Validates the job project manager
 * @param {HTMLElement} jobManager
 * @param {HTMLElement} jobManagerErrorMsg 
 * @returns {Boolean} true if successful validation, false otherwise
 */
function validateJobManager(jobManager, jobManagerErrorMsg) {
    if (jobManager.validity.patternMismatch) {
        jobManager.classList.remove('is-valid');
        jobManager.classList.add('is-invalid');
        jobManagerErrorMsg.innerHTML = 'Must be less than 255 characters';
        return false;
    } else {
        jobManager.classList.add('is-valid');
        jobManager.classList.remove('is-invalid');
        return true;
    }
}

/**
 * Validates the job address
 * @param {HTMLElement} jobAddress
 * @param {HTMLElement} jobAddressErrorMsg 
 * @returns {Boolean} true if successful validation, false otherwise
 */
function validateJobAddress(jobAddress, jobAddressErrorMsg) {
    if (jobAddress.validity.patternMismatch) {
        jobAddress.classList.remove('is-valid');
        jobAddress.classList.add('is-invalid');
        jobAddressErrorMsg.innerHTML = 'Must be less than 255 characters';
        return false;
    } else {
        jobAddress.classList.add('is-valid');
        jobAddress.classList.remove('is-invalid');
        return true;
    }
}

/**
 * Validates end dates for add new job.
 * @param {HTMLElement} jobStartDate
 * @param {HTMLElement} jobEndDate used to validate if start date > end date 
 * @param {HTMLElement} jobEndDateErrorMsg used to change end date's error message if start date > end date 
 * @returns {Boolean} true if successful validation, false otherwise
 */
function validateJobEndDate(jobStartDate, jobEndDate, jobEndDateErrorMsg) {
    // if (jobEndDate.validity.valueMissing) {
    //     jobEndDate.classList.remove('is-valid');
    //     jobEndDate.classList.add('is-invalid');
    //     jobEndDateErrorMsg.innerHTML = 'Expected End Date Must Be Filled';
    //     return false;
    // } else
    if (jobEndDate.valueAsDate < jobStartDate.valueAsDate && jobEndDate.value && jobStartDate.value) {
        jobStartDate.classList.remove('is-valid');
        jobStartDate.classList.add('is-invalid');
        jobEndDate.classList.remove('is-valid');
        jobEndDate.classList.add('is-invalid');
        jobEndDateErrorMsg.innerHTML = 'End Date Must Be After Start Date';
        return false;
    } else {
        jobStartDate.classList.add('is-valid');
        jobStartDate.classList.remove('is-invalid');
        jobEndDate.classList.add('is-valid');
        jobEndDate.classList.remove('is-invalid');
        return true;
    }
}

/**
 * Validates start date for add new job.
 * @param {HTMLElement} jobStartDate
 * @param {HTMLElement} jobStartDateErrorMsg
 * @param {HTMLElement} jobEndDate used to validate if start date > end date 
 * @param {HTMLElement} jobEndDateErrorMsg used to change end date's error message if start date > end date 
 * @returns {Boolean} true if successful validation, false otherwise
 */
function validateJobStartDate(jobStartDate, jobStartDateErrorMsg, jobEndDate, jobEndDateErrorMsg) {
    // if (jobStartDate.validity.valueMissing) { //probably will never run since start date's default is today, but left in anyways in case that ever needs to change
    //     jobStartDate.classList.remove('is-valid');
    //     jobStartDate.classList.add('is-invalid');
    //     jobStartDateErrorMsg.innerHTML = 'Start Date Must Be Filled';
    //     return false;
    // } else 
    if (jobEndDate.valueAsDate < jobStartDate.valueAsDate && jobEndDate.value && jobStartDate.value) { //check if end date is before start date and only check if end date is filled
        jobStartDate.classList.remove('is-valid');
        jobStartDate.classList.add('is-invalid');
        jobEndDate.classList.remove('is-valid');
        jobEndDate.classList.add('is-invalid');
        jobEndDateErrorMsg.innerHTML = 'End Date Must Be After Start Date'; // changes End Date's error message rather than start date (so no duplicate error messages)
        return false;
    } else {
        jobStartDate.classList.add('is-valid');
        jobStartDate.classList.remove('is-invalid');
        // jobEndDate.classList.add('is-valid');
        jobEndDate.classList.remove('is-invalid');
        return true;
    }
}