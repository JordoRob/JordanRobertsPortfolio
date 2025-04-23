function pinPost(button){
    var post_title=button.dataset.title;
    var post_id=button.dataset.id;
    if(confirm("Are you sure you want to pin the post "+post_title+"?")){
            $.ajax({ //Process the form using $.ajax()
                type      : 'POST', //Method type
                url       : 'ajax/pinPost.php', //Your form processing file URL
                data      : {post_id:post_id,pin:1}, //Forms name
                dataType  : 'json',
                success   : function(data){
                    if(!data.allowed){
                        alert("You aren't allowed!");
                    }else if(!data.completed){
                        alert("Huh, that didnt work I guess :(");
                    }else{
                        alert("Post Pinned!");
                    }
                },error: function(){
                    alert("shit");
                }
            });
    }}

    function unpin(button){
        var post_title=button.dataset.title;
        var post_id=button.dataset.id;
        if(confirm("Are you sure you want to unpin the post '"+post_title+"'?")){
                $.ajax({ //Process the form using $.ajax()
                    type      : 'POST', //Method type
                    url       : 'ajax/pinPost.php', //Your form processing file URL
                    data      : {post_id:post_id,pin:0}, //Forms name
                    dataType  : 'json',
                    success   : function(data){
                        if(!data.allowed){
                            alert("You aren't allowed!");
                        }else if(!data.completed){
                            alert("Huh, that didnt work I guess :(");
                        }else{
                            alert("Post Unpinned!");
                        }
                    },error: function(){
                        alert("shit");
                    }
                });
        }}