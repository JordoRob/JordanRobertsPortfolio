function resolve(id,resolved){
    if(resolved==2){
        if(!confirm("Only delete reports that are spam or fake, please resolve if it is legitimate.")){
            return;
        }
    }
        $.ajax({ //Process the form using $.ajax()
    type      : 'POST', //Method type
    url       : 'ajax/resolve.php', //Your form processing file URL
    data      : {id:id,resolved:resolved}, //Forms name
    dataType  : 'json',
    success   : function(data){
        $("#reportsubmit").click();
    },error: function(){
        alert("shit");
    }
});}
$(document).ready(function(){
$(".adminSubmit").click(function(event){
event.preventDefault();
var table=event.currentTarget.getAttribute('name');
$("#"+table+"-body").css("display","block");

var checked=0;
if($("#"+table+"-check").is(':checked')){
checked=1;
}
var compare=$("#"+table+"-type").find(":selected").val();
var term=$("#"+table+"-searchterm").val();
console.log(checked);
if(table=="posts"){
$("#posts-tablebody").children("tr").remove();
$.ajax({ //Process the form using $.ajax()
    type      : 'POST', //Method type
    url       : 'ajax/adminQuery.php', //Your form processing file URL
    data      : {term:term,compare:compare,table:table,checked:checked}, //Forms name
    dataType  : 'json',
    success   : function(data){
        $.each(data, function (index, value) {
            var row = "<tr id="+value.post_id+"><td><a href=account.php?id="+value.user_id+">"+value.username+"</a></td>";
            row+="<td>"+value.user_id+"</td>";
            row+="<td>"+value.post_id+"</td>";
            if(value.pinned==1){
                row+="<td class='titlecol'><a style='color:red;' href='post.php?post="+value.post_id+"&topic="+value.topic_id+"'>"+value.post_title+"</a></td>";
            }else{
                row+="<td class='titlecol'><a href='post.php?post="+value.post_id+"&topic="+value.topic_id+"'>"+value.post_title+"</a></td>";
            }
            row+="<td class='contentcell'>"+value.content+"</td>";
            row+="<td><a href=# class='admin-buttons outerbox' title='Delete Post' onclick='deletePost("+value.post_id+")'>X</a></br>";
            if(value.pinned==1){
                row+="<a href=# class='admin-buttons outerbox' title='Unpin Post' onclick='unpin(this)' data-id="+value.post_id+" data-title='"+value.post_title+"'><img class='flip' src='img/tasks/pin.png'></a></td>";
            }else{
            row+="<a href=# class='admin-buttons outerbox' title='Pin Post' onclick='pinPost(this)' data-id='"+value.post_id+"' data-title='"+value.post_title+"'><img class='flip' src='img/tasks/pin.png'></a></td>";}
            row=$(row);
            $("#posts-tablebody").append(row);
        });
    },error: function(){
        alert("shit");
    }
});}
else {if(table=='users'){
$("#users-tablebody").children("tr").remove();
$.ajax({ //Process the form using $.ajax()
    type      : 'POST', //Method type
    url       : 'ajax/adminQuery.php', //Your form processing file URL
    data      : {term:term,table:table,compare:compare,checked:checked}, //Forms name
    dataType  : 'json',
    success   : function(data){
        $.each(data, function (index, value) {
            var row = "<tr><td><a href=account.php?id="+value.user_id+">"+value.username+"</a></td>";
            row+="<td>"+value.user_id+"</td>";
            row+="<td>"+value.email+"</td>";
            row+="<td>"+value.numPosts+"</td>";
            row+="<td>"+value.numReplies+"</td>";
            row+="<td>"+value.numReports+"</td>";
            if(value.banned==1){
                row+="<td><a href=# class='admin-buttons outerbox' title='Banned'><img src='img/tasks/banned.png'></a></td>";  
            }else{
            row+="<td><a href=# class='admin-buttons outerbox' title='Ban user' onclick='ban("+value.user_id+")'><img src='img/tasks/ban.png'></a></td>";}
            row=$(row);
            $("#users-tablebody").append(row);
        });
    },error: function() {
alert("error?");
}
});
}else if(table=="reports"){
console.log("term:"+compare);
$("#reports-list").children("li").remove();
$.ajax({ //Process the form using $.ajax()
    type      : 'POST', //Method type
    url       : 'ajax/adminQuery.php', //Your form processing file URL
    data      : {term:term,table:table,compare:compare,checked:checked}, //Forms name
    dataType  : 'json',
    success   : function(data){
        $.each(data, function (index, value) {
                               var id = 0;
                               var link = "";
            var villain = link = 'account.php?id='+value.villain_id;
           var hero = link = 'account.php?id='+value.hero_id;
           var link = 'account.php?id='+value.villain_id;
            if (value.focus == "post") {
                id = value.post_id;
                link = 'post.php?post='+value.post_id+"&topic="+value.topic_id;
            }
            if (value.focus == "reply") {
                id = value.reply_id;
                link = 'post.php?post='+value.post_id+"&topic="+value.topic_id+'#'+value.reply_id;
            }
            if (value.focus == "account") {
                id = value.villain_id;
            }
            var row=("<li id='report-"+value.report_id+"'><span class='rowflex-center'><div class='outerbox reportentry' id='resolved-"+value.resolved+"'><a class='hero' href="+hero+">"+value.hero+"</a> reported <a href="+villain+" class='villain'>"+value.villain+"'s</a> <a href="+link+" class='offense'>"+value.focus+" ["+id+"]</a>");
            row+=("</br><div class='innerbox reportbody'>"+value.report+"</br></div>"+value.created_at+"</div><span class='colflex-center'>");
            if(value.resolved==0)
            row+=("<a href='#' onclick='resolve("+value.report_id+",1)' class='resolved outerbox'>Resolve</a>");
            else
            row+=("<a href='#' onclick='resolve("+value.report_id+",0)' class='resolved outerbox'>Reflag</a>");
            row+=("<a href='#' onclick='resolve("+value.report_id+",2)' class='resolved outerbox'>Delete</a></span></span></li>");
            row=$(row);
            $("#reports-list").append(row);
        });
    },error: function() {
alert("error?");
}
});
}
else{
alert("This was your fault not mine.");

}
}
});
});
function ban(userid){
var deleteall=0;
if(confirm("Do you really want to ban this user?")){
if(confirm("Would you like to delete all posts and replies associated with this account?")){
    deleteall=1;
}
$.ajax({ //Process the form using $.ajax()
    type      : 'POST', //Method type
    url       : 'ajax/banUser.php', //Your form processing file URL
    data      : {userid:userid,deleteall:deleteall}, //Forms name
    dataType  : 'json',
    success   : function(data){
        if(data.success==3){
            alert("That did not go right!");
        }else if(data.success==2){
            alert("Try relogging in probably");
        }else if(data.success==1){
            alert("You cannot delete admin users!");
        }else if(data.success==0){
            location.reload();
        }
    },error: function(){
        alert("shit");
    }
})
}}
