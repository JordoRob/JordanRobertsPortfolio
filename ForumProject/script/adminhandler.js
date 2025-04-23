                $(document).ready(function(){
                    $('#adminButton').click(function(event){
                        var password = $("#adminPassword").val();
                        event.preventDefault();
                        $.ajax({ //Process the form using $.ajax()
                            type      : 'POST', //Method type
                            url       : 'ajax/checkAdmin.php', //Your form processing file URL
                            data      : {pass:password}, //Forms name
                            dataType  : 'json',
                                success   : function(data){
                                    if(data.access){
                                        window.location.href="admin.php";
                                    
                                    }else{
                                        $("#adminError").css("display","flex", "font-size","15px","color","red");
                                    };
                                    
                                },
                                error:function(jqxhr, status, exception) {
                                    alert('Exception:', exception);}
                                
                        });
                    });
                     });
                    function adminShow(){
                        document.getElementById("admin").setAttribute("style", "display:block;")
                        document.getElementById("window").setAttribute("style", "filter: brightness(70%);")
                    }
                    function adminClose(){
                        document.getElementById("admin").setAttribute("style", "display:none;")
                        document.getElementById("window").setAttribute("style", "filter: brightness(100%);")
                        document.getElementById("loginError").style.cssText="display:none;";
        
                    }
               
               