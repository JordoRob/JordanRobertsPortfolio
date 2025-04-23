                $(document).ready(function(){
                    $('#loginBut').click(function(event){
                        var username = $("#username").val();
                        var password = $("#password").val();
                         event.preventDefault();
                        $.ajax({ //Process the form using $.ajax()
                            type      : 'POST', //Method type
                            url       : 'ajax/checkLogin.php', //Your form processing file URL
                            data      : {user:username,pass:password}, //Forms name
                            dataType  : 'json',
                                success   : function(data){
                                    if(data.access==0){
                                    location.reload();
                                    
                                    }else if(data.access==2){
                                        document.getElementById("loginError").style.cssText="display:flex; font-size:15px; color:red;";
                                    }else if(data,access==1){
                                        alert ("User has been banned.");
                                    };
                                    
                                },
                                error:function(jqxhr, status, exception) {
                                    alert('Exception:', exception);}
                                
                        });
                    });
                    
                   
                });
                function logout(){
                    $.ajax({ //Process the form using $.ajax()
                            type      : 'POST', //Method type
                            url       : 'ajax/logout.php', //Your form processing file URL
                            data      : {}, //Forms name
                            dataType  : 'json',
                                success   : function(data){
                                    if(data.success){
                                    location.reload();
                                    
                                    }else{
                                        alert("not success");
                                    };
                                    
                                },error : function(){
                                    alert("error?");
                                }
                            });}
                            function loginShow(){
                                document.getElementById("login").setAttribute("style", "display:block;")
                                document.getElementById("window").setAttribute("style", "filter: brightness(70%);")
                            }
                            function loginClose(){
                                document.getElementById("login").setAttribute("style", "display:none;")
                                document.getElementById("window").setAttribute("style", "filter: brightness(100%);")
                                document.getElementById("loginError").style.cssText="display:none;";
                
                            }
               