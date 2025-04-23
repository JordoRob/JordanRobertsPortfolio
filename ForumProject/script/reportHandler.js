function createReport(button){
$("#reportForm").css("display","block");
var focus = button.dataset.focus;
var villainname = button.dataset.username;
var villainid = button.dataset.userid;
var id = button.dataset.focusid;
document.getElementById("window").setAttribute("style", "filter: brightness(70%);");
$("#villain-name").text(villainname);
$("#focus-name").text(focus);
$("#reportBut").click(function(){
    var report=$("#reportreason").val();
    $.ajax({ //Process the form using $.ajax()
        type      : 'POST', //Method type
        url       : 'ajax/createReport.php', //Your form processing file URL
        data      : {focus:focus,focusid:id,villainid:villainid,report:report}, //Forms name
        dataType  : 'json',
        success   : function(data){
            if(data.submitted){
                var save = $("#reportForm").html();
                $("#reportForm").text("Thank you!");
               $("#reportForm").fadeOut(1000,function(){
                $("#reportForm").html(save);
                reportClose();
               });
            }
        },error: function(){
            alert("uh ohh");
        }
    })
})
}
function reportClose(){
$("#reportForm").css("display","none");
document.getElementById("window").setAttribute("style", "filter: brightness(100%);");
}