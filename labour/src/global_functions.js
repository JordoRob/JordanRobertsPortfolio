// For dropdown animations
$(document).ready(function () {
    $(".dropdown").click(function () {
        // Closes all other dropdowns when mouse is clicked on a dropdown button
        $(".dropdown-content").not($(this).children(".dropdown-content")).slideUp("fast");

        // Toggles the current dropdown when mouse is clicked on a dropdown button
        $(this).children(".dropdown-content").slideToggle("fast");
    });

    // Closes all dropdowns when mouse is clicked not on a dropdown button
    $(document).on("click", function (event) {
        var $trigger = $(".dropdown");
        if ($trigger !== event.target && !$trigger.has(event.target).length) {
            $(".dropdown-content").slideUp("fast");
            $(".status-drop").removeClass('open');
        }
    });
});

/**
 * Prevents XSS attacks by escaping HTML characters
 * @param {*} str - The string to be escaped
 * @returns - The escaped string
 */
function escapeHTML(str) {
    return str.replace(/[&<>"'`=\/]/g, function (s) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#x27;',
            '/': '&#x2F;',
            '`': '&#x60;',
            '=': '&#x3D;'
        }[s];
    });
}


// Variables to keep track of the messages as they are added and removed
var counter = 0, trailer = 0, stack = 0;

/**
 * Sends a auto removing message to a popup container
 * @param {String} message The message to be sent
 * @param {Boolean} error Whether the message is an error message or not
 */
function send_message(message, error = false) {
    // Append the message to the message_container
    $("#message_container").append(
        `
        <div id="ajax_message_${counter}" class="ajax_message" style="display: none; ${error ? 'background-color: #ff0f004d;' : ''}">${escapeHTML(message)}</div>
        `
    );
    stack++;

    if (stack === 1) {
        $(`#ajax_message_${counter}`).show();
        $("#message_container").slideDown();
    } else {
        $(`#ajax_message_${counter}`).slideDown();
    }

    counter++;

    // Remove the appended element after 5 seconds
    setTimeout(function () {
        var local_trailer = trailer;
        // Check if is last message
        if (stack === 1) {
            // If the container is empty, hide it again
            $("#message_container").slideUp(function () {
                $(`#ajax_message_${local_trailer}`).remove();
            });
        } else {
            $(`#ajax_message_${local_trailer}`).slideUp(function () {
                $(`#ajax_message_${local_trailer}`).remove();
            });
        }
        stack--;
        trailer++;
    }, 5000); // 5000 milliseconds = 5 seconds
}

var open_overlay_count = 0; // Keeps track of how many overlays are open, prevents ajax_search from being called when an overlay is still open
/**
 * Generates an overlay with a mask behind it
 * @param {String} id - id of the overlay (used for targeting the overlay when closing)
 * @param {String} width - width of the overlay
 * @param {String} height - min-height of the overlay
 * @param {Int} z_index - z-index of the overlay
 */

function generate_overlay(id = 'main-overlay', width = '60em', height = '35em', mask_close = true) {
    open_overlay_count++;

    z_index = (6 + (open_overlay_count * 2));

    if (width == null) width = 'inherit';
    if (height == null) height = 'inherit';
    $(document.body).prepend(`<div class="main-overlay" id='${id}'></div>`);
    $(`#${id}`).css({
        "width": width,
        "min-height": height,
        "z-index": z_index,
        "height": "fit-content"
    });
    var overlay = `<div id='overlay_mask_${id}' class='overlay_mask' ${mask_close ? `onclick='closeOverlay("${id}");'` : ''}></div>`;


    $(document.body).prepend(overlay);
    $(`#overlay_mask_${id}`).css({
        "z-index": z_index - 1
    });
}

// used for flashing the save/edit buttons for job overlay and employee overlay
var fade_emp;
var fade_job;

/**
 * Closes an overlay and removes the mask behind it
 * @param {String} id - id of the overlay (used for targeting the overlay when closing)
 */
function closeOverlay(id = "main-overlay") {

    if (id == "job-overlay")
        clearInterval(fade_job);
    else
        clearInterval(fade_emp);
    $(`#${id}`).remove();
    $("#overlay_add_employee").hide();

    $(`#overlay_mask_${id}`).remove();
    if (open_overlay_count > 0) open_overlay_count--;
    if (open_overlay_count == 0 && typeof no_ajax === 'undefined') { // no_ajax is set when page does not have ajax_search function (admin_functions.php line 26)
        ajax_search();
    }
}

/**
 * Copies a string to the clipboard and sends a message to the user
 * @param {*} text - The string to be copied
 */
function copyToClipboard(text) {
    navigator.clipboard.writeText(text);
    send_message("Copied generated password to clipboard");
}