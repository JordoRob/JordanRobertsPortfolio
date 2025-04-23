function deletePost(post_id){
if(confirm("Are you sure you want to delete this post forever (and ever)?")){
        $.ajax({ //Process the form using $.ajax()
            type      : 'POST', //Method type
            url       : 'ajax/deletePost.php', //Your form processing file URL
            data      : {post_id:post_id}, //Forms name
            dataType  : 'json',
            success   : function(data){
                if(!data.allowed){
                    alert("You aren't allowed!");
                }else if(!data.completed){
                    alert("Huh, that didnt work I guess :(");
                }else{
                    $("#"+post_id).remove();
                }
            },error: function(){
                alert("shit");
            }
        });
}}