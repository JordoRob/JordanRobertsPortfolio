


var profilePic="img/user/sword.png";
function addPic(src, id){
    document.getElementById("1").classList.remove("innerbox");
    document.getElementById("2").classList.remove("innerbox");
    document.getElementById("3").classList.remove("innerbox");
    document.getElementById("4").classList.remove("innerbox");
    document.getElementById("5").classList.remove("innerbox");
    profilePic=src;
    //alert(src);
    //document.getElementById(id).style.cssText="border: 1px solid black";
    document.getElementById(id).classList.add("innerbox");
};
function customPic(){
alert();

};
$(document).ready(function(){

    $('.registerButton').click(function(){

        var username = $("#username1").val();
        console.log(username+" hi");
        var password1 = $("#password1").val();
        var password2 = $("#password2").val();
        var email1 = $("#email1").val();
        var email2 = $("#email2").val();
        var check = true;

        if(username==""||password1==""||password2==""||email1==""||email2==""){
            document.getElementById("emptyFields").style.cssText="font-size:22px; color:red; display:inline;";
            check=false;
            document.getElementById("passwordError").style.cssText="display:none;";
            document.getElementById("emailError").style.cssText="display:none;";
        }else{
            document.getElementById("emptyFields").style.cssText="display:none;";        

            
            if(password1!=password2){
                document.getElementById("passwordError").style.cssText="font-size:22px; color:red; display:inline;";
                check=false;
            }else{
                document.getElementById("passwordError").style.cssText="display:none;";

                if(!(/^(?=.*\d)(?=.*[!@#$%^&*])(?=.*[A-Z]).{8,}$/.test(password1))){
                    document.getElementById("passwordStrength").style.cssText="font-size:22px; color:red; display:inline;";
                    check=false;
                }else{
                    document.getElementById("passwordStrength").style.cssText="display:none;";
                }
            }

            
            if(email1!=email2){
                document.getElementById("emailError").style.cssText="font-size:22px; color:red; display:inline;";
                 check=false;

                }else{
                    if(!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email1))){
                        document.getElementById("emailError").style.cssText="font-size:22px; color:red; display:inline;";
                        check=false;

                            }else{
                            document.getElementById("emailError").style.cssText="display:none;";
                            }
                               
                } 
        }
        
        

        if(check){
            $.ajax({ //Process the form using $.ajax()
                        type      : 'POST', //Method type
                        url       : 'ajax/checkRegister.php', //Your form processing file URL
                        data      : {user:username,pass:password1,email:email1,pic:profilePic}, //Forms name
                        dataType  : 'json',
                            success   : function(data){
                                if(data.userCheck&&data.emailCheck){
                                window.location.replace('account.php?id='+data.userid);
                                
                                }else{                      
                                    if(!data.userCheck){
                                        document.getElementById("usernameError").style.cssText="font-size:22px; color:red; display:inline;";
                                    }
                                    if(!data.emailCheck){
                                        document.getElementById("emailTaken").style.cssText="font-size:22px; color:red; display:inline;";
                                    }
                                };
                                
                            },
                            error:function(jqxhr, status, exception) {
                                alert('Exception:', exception);}
                            
                    });

        }
        
    });
    
   
});
