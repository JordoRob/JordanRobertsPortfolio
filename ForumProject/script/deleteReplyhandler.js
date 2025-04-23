function DelReply(replyid){
        if(confirm("Are you sure you want to delete this reply forever (and ever)?")){
                $.ajax({ //Process the form using $.ajax()
                    type      : 'POST', //Method type
                    url       : 'ajax/deleteReply.php', //Your form processing file URL
                    data      : {replyid:replyid}, //Forms name
                    dataType  : 'json',
                    success   : function(data){
                        if(!data.allowed){
                            alert("You aren't allowed!");
                        }else if(!data.completed){
                            alert("Huh, that didnt work I guess :(");
                        }else{
                            location.reload();
                        }
                    },error: function(){
                        alert("shit");
                    }
                });
        }}