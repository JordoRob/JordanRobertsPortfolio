$(document).ready(function(){
    var userid =$("#sessionUSERID").val();
    var postid = $(".work").attr("name");
    var best=$("#biggest").val();
    var admin=$("#adminVer").val();
    console.log(admin);
    console.log(best);
    $.ajax({ //Process the form using $.ajax()
    type      : 'POST', //Method type
    url       : 'ajax/loadreplies.php', //Your form processing file URL
    data      : {id:postid}, //Forms name
    dataType  : 'json',
    success   : function(data){
        $.each(data, function (index, value) {
        var reply = "<li><ul id="+value.reply_id+" class='replies'>";
        reply= reply+ "<div class='posts-reply'>";
        reply= reply+  "<div class='innerbox posts-body'>";
        reply= reply+  "<span style='font-weight:bold;'><a href='account.php?id="+value.user_id+"'><img class='reply-image' alt='profile picture' src="+value.profile_pic+"><span class='reply-username'>"+value.username+" said:</span></a>";
        reply= reply+  "<span class=reply-date>"+value.created_at+"</span></span>";
        reply= reply+  "<div class='post-content'>"+value.content+"</div>";
        if(userid!=null){
            reply=reply+"<span class='replyButton outerbox' id='button"+value.reply_id+"' onclick='reply("+value.reply_id+")'>Reply</span>";
            if(value.banned==0){
               reply=reply+ "<span class='replyButton outerbox' id='report"+value.reply_id+"' onclick='createReport(this)' data-focus=\"reply\" data-focusid='"+value.reply_id+"' data-userid='"+value.user_id+"' data-username='"+value.username+"'>Report</span>";
            }
            if(userid==value.user_id||admin==userid){
                reply=reply+"<span class='replyButton outerbox' onclick='DelReply("+value.reply_id+")'>Delete</span>";
            }
            reply=reply+"</div>";
        }else{reply=reply+"<span class='replyButton innerbox' id='button"+value.reply_id+"' onclick='loginShow()'>Reply</span><span class='replyButton innerbox' id='report"+value.reply_id+"' onclick='loginShow()'>Report</span> </div>";
        }
        reply= reply+  "</div><a href='#"+value.reply_id+"' class='thread-nav-reply'></a></ul></li>";  
        reply=$(reply);
        var parent = $("#"+value.parent_id);
        parent.append(reply);
        $(parent).children(".thread-nav-reply").css("display","block");
        if(value.reply_id>best)best=value.reply_id;
        });
    },
    error:  function(jqxhr, status, exception) {
     }
});
setInterval(newRep, 30000);
function newRep(){
    console.log(best);
    var postid = $(".work").attr("name");
    $.ajax({ //Process the form using $.ajax()
    type      : 'POST', //Method type
    url       : 'ajax/loadreplies.php', //Your form processing file URL
    data      : {id:postid,curRep:best}, //Forms name
    dataType  : 'json',
    success   : function(data){
        $.each(data, function (index, value) {
        var reply = "<li><ul id="+value.reply_id+" class='replies'>";
        reply= reply+ "<div class='posts-reply'>";
        reply= reply+  "<div class='innerbox posts-body'>";
        reply= reply+  "<span style='font-weight:bold;'><a href='account.php?id="+value.user_id+"'><img class='reply-image' alt='profile picture' src="+value.profile_pic+"><span class='reply-username'>"+value.username+" said:</span></a>";
        reply= reply+  "<span class=reply-date>"+value.created_at+"</span></span>";
        reply= reply+  "<div class='post-content'>"+value.content+"</div>";
        if(userid!=null){
            reply=reply+"<span class='replyButton outerbox' id='button"+value.reply_id+"' onclick='reply("+value.reply_id+")'>Reply</span>";
            if(value.banned==0){
               reply=reply+ "<span class='replyButton outerbox' id='report"+value.reply_id+"' onclick='createReport(this)' data-focus=\"reply\" data-focusid='"+value.reply_id+"' data-userid='"+value.user_id+"' data-username='"+value.username+"'>Report</span>";
            }
            if(userid==value.user_id||admin==userid){
                reply=reply+"<span class='replyButton outerbox' onclick='DelReply("+value.reply_id+")'>Delete</span>";
            }
            reply=reply+"</div>";
        }else{reply=reply+"<span class='replyButton innerbox' id='button"+value.reply_id+"' onclick='loginShow()'>Reply</span><span class='replyButton innerbox' id='report"+value.reply_id+"' onclick='loginShow()'>Report</span> </div>";
        }
        reply= reply+  "</div><a href='#"+value.reply_id+"' class='thread-nav-reply'></a></ul></li>";  
        reply=$(reply);
        var parent = $("#"+value.parent_id);
        parent.append(reply);
        $(parent).children(".thread-nav-reply").css("display","block");
        if(value.reply_id>best)best=value.reply_id;
        });
    },
    error:  function(jqxhr, status, exception) {
     }
});
}

});