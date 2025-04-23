function refresh(page, last_check) {
    let secs = localStorage.getItem('timer');
    if (secs == null) {
        secs = 30;
    }
    const COUNTDOWN_SECS = secs;
    let timeRefreshLabel = $('#refresh_counter');
    let timeRefreshIcon = $("#refresh_icon");
    let refreshButton = $("#refresh_button");
    let currCountdown = 0; // counts up 0,1,2, etc to make the countdown tick down
    let update = false; // used for "Updates Added!"


    refreshButton.on("click", function () {
        clearInterval(refresh_interval);  //get rid of the interval
        currCountdown = COUNTDOWN_SECS;   //make it pretend like we hit auto-refresh time
        refreshHandler();

        if (COUNTDOWN_SECS != 0) //if this isnt disabled, go right on ahead
            refresh_interval = setInterval(refreshHandler, 1000);

        setTimeout(function () {  //make it so the cool spin continues to work
            timeRefreshIcon.removeClass("refresh_transition");
            timeRefreshIcon.css("rotate", "0deg");
            if(update&&COUNTDOWN_SECS == 0){
                refreshHandler();
                update=false;
            }
        }, 1000);
        setTimeout(function () {

            if (COUNTDOWN_SECS == 0) {
                timeRefreshLabel.removeClass('refresh_updates');
                timeRefreshIcon.removeClass('refresh_updates');
                timeRefreshLabel.html("");
            }
        }, 4000)
    });


    if (COUNTDOWN_SECS != 0) {
        var refresh_interval = setInterval(refreshHandler, 1000);
    }



    function refreshHandler() {
        if (update) {
            if (currCountdown == 0) {   //if the update was just found give the cool styles and send a message
                timeRefreshLabel.html("!");
                timeRefreshLabel.addClass('refresh_updates');
                timeRefreshIcon.addClass('refresh_updates');
                send_message("Updates Found and Applied");
            }
            if (currCountdown == 4) {
                //function has been paused for 4 seconds, restart it
                currCountdown = 0;
                timeRefreshLabel.removeClass('refresh_updates');
                timeRefreshIcon.removeClass('refresh_updates');;
                update = false;
            }

            currCountdown++;
        }
        else {
            let num = COUNTDOWN_SECS - currCountdown;
            refreshButton.attr("title", "Click to Refresh Manually, Auto Refresh in " + num + " seconds");
            if (num == 0) {
                num = "";
            }
            timeRefreshLabel.html(num);
            if (currCountdown == COUNTDOWN_SECS) {  //It is time to refresh
                timeRefreshIcon.addClass("refresh_transition"); //give it the class with the transition
                timeRefreshIcon.css("rotate", "360deg"); //spin 'er up bob
                currCountdown = 0;
                $.ajax({
                    url: "PortfolioWebsite/labour/src/refresh/check-update.php",
                    type: 'POST',
                    data: {
                        page: page, //determines which tables to check  
                        last_check: last_check  //when to compare against
                    },
                    dataType: "json",
                    success: function (data) {
                        if (data[0]) {
                            update = true;

                            last_check = Math.round(Date.now() / 1000);
                            ajax_search(); //ajax search gets called in both employees.php and job-view.php
                            if (page == "job") {
                                ajax_totals();  //job has some other stuff too
                                ajax_employees();
                                job_inactive_update();
                            }
                        }
                        return (Math.round(Date.now() / 1000));
                    },
                    error: function () {
                        return (Math.round(Date.now() / 1000));
                    }
                });
            } else {
                if (currCountdown == 0) {
                    timeRefreshIcon.removeClass("refresh_transition");  //if we just started counting again, get rid of this stuff so she continues to spin
                    timeRefreshIcon.css("rotate", "0deg");   //its kinda like regular maintenance on a car yknow?
                }
                currCountdown++;
            }
        }
    }


}

