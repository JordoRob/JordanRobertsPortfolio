$(document).ready(function(){

    $('#forgotPasswordButton').click(function(){
        document.getElementById("userOrEmailDNEFP").style.cssText="display:none;"
        var username = $("#usernameFP").val();
        var email1 = $("#email1FP").val();
        var email2 = $("#email2FP").val();
        var check = true;

        if(username==""||email1==""||email2==""){
            document.getElementById("emptyFieldsFP").style.cssText="font-size:22px; color:red; display:inline;";
            document.getElementById("emailErrorFP").style.cssText="display:none;";
            check=false;
        }else{
            document.getElementById("emptyFieldsFP").style.cssText="display:none;";       

            if(email1!=email2){
                document.getElementById("emailErrorFP").style.cssText="font-size:22px; color:red; display:inline;";
                check=false;

                }else{
                    document.getElementById("emailErrorFP").style.cssText="display:none;";     
                } 
        }
        
        

        if(check){
            $.ajax({ //Process the form using $.ajax()
                        type      : 'POST', //Method type
                        url       : 'ajax/sendEmail.php', //Your form processing file URL
                        data      : {user:username,email:email1}, //Forms name
                        dataType  : 'json',
                            success   : function(data){
                                if(data.access==true){
                                alert("An email has been sent, please check your inbox.");
                                }else if(data.access==false){                      
                                document.getElementById("userOrEmailDNEFP").style.cssText="font-size:22px; color:red; display:inline;"
                                }else if(data.access==5){
                                    alert("e-mail failed to send");
                                }
                                
                            },
                            error:function(jqxhr, status, exception) {
                                alert('Exception:', exception);}
                            
                    });

        }
        
    });
    
   
});
