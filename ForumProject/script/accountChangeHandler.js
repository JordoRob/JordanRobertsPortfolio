$(document).ready(function(){
    $('.passwordButton').click(function(){
        var oldP = $("#oldp").val();
        var verify = $("#newpassverify").val();
        var newP = $("#newpass").val();
        
        document.getElementById("passwordMatchError").style.cssText="display:none;";
        document.getElementById("AccountpStrength").style.cssText="display:none;";
         if(!newP==verify){
                document.getElementById("passwordMatchError").style.cssText="display:flex; font-size:15px; color:red;";
         }else{
            if(!(/^(?=.*\d)(?=.*[!@#$%^&*])(?=.*[A-Z]).{8,}$/.test(newP))){
                document.getElementById("AccountpStrength").style.cssText="font-size:15px; color:red; display:inline-block;";

            }else{
                $.ajax({ //Process the form using $.ajax()
            type      : 'POST', //Method type
            url       : 'ajax/changePass.php', //Your form processing file URL
            data      : {oldPass:oldP,newPass:newP}, //Forms name
            dataType  : 'json',
                success   : function(data){
                    if(data.access==0){
                    pwChangeClose();
                    alert ("Success.");
                    }else if(data.access==1){
                    alert ("Password entered incorrect.");
                    }else{
                    pwChangeClose();
                    alert ("You are not logged in.");
                    }
                    
                },
                error:function(jqxhr, status, exception) {
                    alert('Exception:', exception);}
                
        });
            }
            
         }
        
    });
    $('.emailButton').click(function(){
        var oldEmail = $("#oldEmail").val();
        var newEmail = $("#newEmail").val();
        var password = $("#emailPassword").val();
        document.getElementById("emailMatchError").style.cssText="display:none;";
         if(!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(newEmail))){
            document.getElementById("emailMatchError").style.cssText="display:block; font-size:15px; color:red;";
         }else{
            $.ajax({ //Process the form using $.ajax()
                        type      : 'POST', //Method type
                        url       : 'ajax/changeEmail.php', //Your form processing file URL
                        data      : {newE:newEmail,pass:password}, //Forms name
                        dataType  : 'json',
                            success   : function(data){
                                if(data.access==0){
                                    emailChangeClose();
                                    alert ("Success.");
                                    }else if(data.access==1){
                                    alert ("Password entered incorrect.");
                                    }else{
                                    emailChangeClose();
                                    alert ("You are not logged in.");
                                    }
                                
                            },
                            error:function(jqxhr, status, exception) {
                                alert('Exception:', exception);}
                            
                    });
         }
        
    });
   
    $('#uploadButton').click(function(){
        $.ajax({
            type: "POST",
            url: 'ajax/accountUploadPhoto.php',
            data: new FormData($('#imageForm')[0]),
            processData: false,
            contentType: false,
            dataType:'json',
            success: function (data) {
                console.log(data.access);
                if(data.access==0){
                    location.reload(true);
                    }else if(data.access==1){
                        alert ("File too large.");
                    }else if(data.access==2){
                        alert("Failed to upload image.");
                    }else if(data.access==3){
                        alert ("You have not selected any new photos.");
                    }else if(data.access==3){
                        alert ("You are not logged in.");
                    }
            },
            error: function (jqXHR, exception) {
                var msg = '';
                if (jqXHR.status === 0) {
                    msg = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status == 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                console.log(msg);
            },
        });
        
    });
    $('#accountBioChangeButton').click(function(){
        document.getElementById("accountBioCurrent").setAttribute("style", "display:none;")
        document.getElementById("accountBioChangeButton").setAttribute("style", "display:none;")
        
        document.getElementById("newBioInput").setAttribute("style", "display:inline;")
        document.getElementById("submitAccountBioChangeButton").setAttribute("style", "display:inline;")
        
    });
    $('#submitAccountBioChangeButton').click(function(){
        var newBio = $("#newBioInput").val();
            $.ajax({ //Process the form using $.ajax()
                        type      : 'POST', //Method type
                        url       : 'ajax/changeBio.php', //Your form processing file URL
                        data      : {newBio:newBio}, //Forms name
                        dataType  : 'json',
                            success   : function(data){
                                if(data.access==0){
                                    location.reload(true);
                                    }else{
                                        alert("Something went wrong.");
                                    }
                                
                            },
                            error:function(jqxhr, status, exception) {
                                alert('Exception:', exception);}
                            
                    });
         
        
    });
});

            function pwChangeShow(){
                document.getElementById("changePw").setAttribute("style", "display:flex;")
                document.getElementById("window").setAttribute("style", "filter: brightness(70%);")
            }
            function pwChangeClose(){
                document.getElementById("changePw").setAttribute("style", "display:none;")
                document.getElementById("window").setAttribute("style", "filter: brightness(100%);")
            }
            function emailChangeShow(){
                document.getElementById("changeEmail").setAttribute("style", "display:flex;")
                document.getElementById("window").setAttribute("style", "filter: brightness(70%);")
            }
            function emailChangeClose(){
                document.getElementById("changeEmail").setAttribute("style", "display:none;")
                document.getElementById("window").setAttribute("style", "filter: brightness(100%);")
            }
