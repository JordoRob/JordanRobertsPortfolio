    function updateScore(button){
        var postid=$(".work").attr("name");
        var userid =$("#sessionUSERID").val();
        var pressed = $(button).html();
        if(pressed=='Dislike'){
            var rating=-1;
        }else if(pressed=='Like'){
            var rating=1;
        }
            if($(button).attr('class').includes(pressed)){
                rating=0;
            }
            $.ajax({ //Process the form using $.ajax()
    type      : 'POST', //Method type
    url       : 'ajax/updatescore.php', //Your form processing file URL
    data      : {post:postid,rating:rating}, //Forms name
    dataType  : 'json',
    success   : function(data){
        console.log(rating);
        var score=data.score;
        if(data.score==null){
            score=0;
        }
        if(rating==0){
            $(button).removeClass(pressed);
        }if(rating==1){
            $(button).addClass(pressed);
            var away = $(button).siblings();
            $(away).removeClass('Dislike');

        }if(rating==-1){
            $(button).addClass(pressed);
            var away = $(button).siblings();
            $(away).removeClass('Like');
        }
        $("#scorecount").html(score);
    },
    error:  function(jqxhr, status, exception) {
     alert('Exception:', exception);}
});
}
function reply(parentid){
console.log(parentid);
$(".type-reply").remove();
var currentuser = $("#sessionUSERNAME").val();
var currentuserpic = $("#sessionUSERPIC").val();
if(parentid==0){
            var parent = $("#grandpa");
            var user = $("#mainUser").text();
            user+=" as <img class='reply-image' src='"+currentuserpic+"'> "+currentuser+": ";
        }else{
            var parent=$("#"+parentid);   
var user = $("#"+parentid).find(".reply-username").first().text();
user= user.replace("said","as <img class='reply-image' src='"+currentuserpic+"'> "+currentuser);}
var reply = "<li class='type-reply' id='input"+parentid+"'><ul class='replies'>";
        reply= reply+ "<div class='posts-reply'>";
        reply= reply+  "<div class='innerbox posts-body'>";
        reply= reply+  "<span style='font-weight:bold;'><span class='reply-username'>Reply to "+user+" </span></a>";
        reply= reply+  "<span class=reply-date>Now</span></span>";
        reply= reply+  "<div class='post-content'><textarea name='reply-content' id='reply-content' style='width:99%;' rows=5></textarea>";
        reply= reply+ "<label id='replyLength' for='reply-content' style='font-size:0.8em;'>500</label></div>";
        reply=reply+ "<span class='replyButton outerbox' onclick='sendreply("+parentid+")'>Post</span><span class='replyButton outerbox' onclick='closeReply("+parentid+")'>Cancel</span> </div>";
        reply=reply+"</div></ul></li>";
        reply=$(reply);
        
        if(parent.find("li").first().length)
        parent.find("li").first().prepend(reply);
        else
            parent.append(reply);
            $("#reply-content").keyup(function(){
                typed=$("#reply-content").val().length;
                $("#replyLength").text(500-typed);
                if(typed>500){
                    $("#replyLength").css("color","red");
                }else{
                    $("#replyLength").css("color","black");
                }
            });
}
function closeReply(parentid){
$("#input"+parentid).remove();
}
function sendreply(parentid){
var postid=$(".work").attr("name");
var text=$("#reply-content");
if(text.val().length==0||text.val().length>500){
    text.css("border","1px solid red");
    text.keyup(function(){
        if(text.val().length<500)
        text.css("border","1px solid black");
    })
}else{
    var content=text.val();
$.ajax({ //Process the form using $.ajax()
    type      : 'POST', //Method type
    url       : 'ajax/createReply.php', //Your form processing file URL
    data      : {postid:postid,parentid:parentid,content:content}, //Forms name
    success   : function(){
        location.reload();
    },error:function(){
        alert("idfk");
    }
});
}}