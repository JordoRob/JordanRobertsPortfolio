function add_emp_overlay() {
    // Display the Add Employee Overlay - If it hasn't been shown yet, create the html, if it has just show the old (that way you can click off accidentally without it deleting)
    if($("#overlay_add_employee").length){
    if (!$("#overlay_add_employee").data("shown")) {
        $("#overlay_add_employee").data("shown", 1);}
    }else{
        $(document.body).prepend("<div id='overlay_add_employee' class='main-overlay' data-shown=0></div>");
        $.ajax({
            type: "POST",
            url: "/labour/src/Employee Page/add-employee-overlay.php",
            async: false,
            success: function (html) {
                $("#overlay_add_employee").html(html); // Populate results div with ajax response
            }})
    }

    $("#overlay_add_employee").show();
    // add onclick listener to around the overlay to hide it
    $(document.body).prepend("<div id='overlay_mask_main-overlay' class='overlay_mask' onclick='hideEmpOverlay(true)'></div>");

    // masks for phone fields
    $('#phone_datafield').on('input', function (e) {
        phoneMask(e);
    });
    $('#phone_datafield_secondary').on('input', function (e) {
        phoneMask(e);
    });

    // submit button
    $("#add_emp_button").click(function (event) {
        event.preventDefault();
        var form = $("#add_employee")[0];
        if (validate_Form(form)) {
            // disable buttons and make it say "processing"
            $("#add_emp_button").prop('disabled', true);
            $("#add_emp_button").html('Processing');
            $("#add_emp_button_reset").prop('disabled', true);

            var formData = new FormData(form);//send formdata to php
            $.ajax({
                url: "/labour/src/Employee Page/add-employee.php",
                type: 'post',
                data: formData,
                contentType: false,
                dataType: "json",
                processData: false,
                success: function (data) { // successful employee submit
                    if (data[0]) { // add-employee.php returns an array -> [true, new_user_id] if succesful
                        hideEmpOverlay(false);
                        $("#overlay_add_employee").data("shown", 0);
                        employeeDetails(data[1]);
                    }
                    else { // add-employee.php returns an array -> [false, "error message"] if unsuccesful
                        send_message(data[1], true);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown){
                    send_message(JSON.parse(jqXHR.responseText)[1], true); 
                    $("#add_emp_button").prop('disabled', false);
                    $("#add_emp_button").html('Add Employee');
                    $("#add_emp_button_reset").prop('disabled', false);
                },
                always: function (data) {
                    // re-enable buttons after
                    $("#add_emp_button").prop('disabled', false);
                    $("#add_emp_button").html('Add Employee');
                    $("#add_emp_button_reset").prop('disabled', false);
                }
            });
        }
    });
    // reset button
    $("#add_emp_button_reset").click(function (event) {
        $("#file_upload_label span").css("display", "");
        $("#file_upload_label").css("background", "");
        $("#file_upload_label span").text("Click to Upload New Image");
        resetError();
    });
}

// adds "Image Uploaded" styling to the add employe pfp
function addEmpImg() {
    $("#file_upload_label span").text("Image Uploaded");//Show them that the image has been accepted
    $("#file_upload_label span").css("display", "block");
    $("#file_upload_label").css("background", "rgba(236, 105, 97, 0.8)");
}
function validate_Form(form) {
    var isValid = true;
    // add event listener to each field that validates them when changed
    $(form).find('input, select').each(function () {
        fieldName = $(this).attr("name");


        if ($(this).attr("required") == "required") {
            if ($(this).val() === null || $(this).val() === '') {   //if the required fields are empty, notify user and add listener to remove error message
                $(this).addClass("is-invalid");
                send_message("Please fill all required fields", true);
                $(this).on("change", function () {
                    var required=true;
                    $("*[required]").each(function () {
                        console.log($(this).attr('name'));
                        if ($(this).val().length < 1) {
                            required=false;
                        }else{
                            $(this).removeClass("is-invalid");
                            $(this).addClass("is-valid");
                        }
                    });
                });
                isValid = false;
            }else{
                $(this).addClass("is-valid");
                $(this).on("change", function () {
                    $(this).removeClass("is-valid");
                });
            }
        }

        if (fieldName == "phone" || fieldName == "phoneSec") {
            if ($(this).val().length > 0 && $(this).val().length < 14) {//if phone numbers are too short, notify and add key listener to remove error message
                $(this).addClass("is-invalid");
                $("#overlay_" + fieldName + "_error").css("visibility","unset");;
                $("#overlay_" + fieldName + "_error").text("Phone Number too short");
                $(this).on("keyup", function () {
                    if ($(this).val().length == 0 || $(this).val().length == 14) {
                        $(this).removeClass("is-invalid");
                        $("#overlay_" + $(this).attr("name") + "_error").css("visibility","hidden");;
                    }
                });
                isValid = false;
            }else{
                $(this).addClass("is-valid");
                $(this).on("keyup", function () {
                    $(this).removeClass("is-valid");
                });
            }
        }

        if (fieldName == "name") {
            if ($(this).val().length > 100) {   //if the name field is too long, notify user and add listener to remove error message
                    $(this).addClass("is-invalid");
                    $("#overlay_" + fieldName + "_error").css("visibility","unset");;
                    $("#overlay_" + fieldName + "_error").text("Name too long!");
                    $(this).on("keyup", function () {
                    if ($(this).val().length < 100)
                        $(this).removeClass("is-invalid");
                    $("#overlay_" + $(this).attr("name") + "_error").css("visibility","hidden");;
                });
                isValid = false;
            }else{
                $(this).addClass("is-valid");
                $(this).on("keyup", function () {
                    $(this).removeClass("is-valid");
                });
            }
        }

        if (fieldName == "email") { //if the email field is too long, notify user and add listener to remove error message
            if ($(this).val().length > 200) {
                $(this).addClass("is-invalid");
                $("#overlay_" + fieldName + "_error").css("visibility","unset");;
                $("#overlay_" + fieldName + "_error").text("Email too long!");
                $(this).on("keyup", function () {
                    if ($(this).val().length < 200)
                        $(this).removeClass("is-invalid");
                    $("#overlay_" + $(this).attr("name") + "_error").css("visibility","hidden");;
                });
                isValid = false;
            }else{
                $(this).addClass("is-valid");
                $(this).on("keyup", function () {
                    $(this).removeClass("is-valid");
                });
            }
        }

        if(fieldName == "birthday"||fieldName=="datehired"){
            $(this).addClass("is-valid");
        }
    });
    return isValid;
}
function hideEmpOverlay(retain) {
    if(retain){
    $("#overlay_add_employee").hide();}
    else{
        $("#overlay_add_employee").remove(); 
    }
    $("#overlay_mask_main-overlay").remove();
    resetError();
    $("#add_emp_button").off("click");
}
function resetError() {  //gets rid of errors when they hide the page
    $(".is-invalid").removeClass("is-invalid");
    $(".is-valid").removeClass("is-valid");
    $(".overlay_error").css("visibility","hidden");;
}