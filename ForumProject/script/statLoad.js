function loadStat(){
    $.ajax({ //Process the form using $.ajax()
        type      : 'POST', //Method type
        url       : 'ajax/statLoad.php', //Your form processing file URL
        data      : {}, //Forms name
        success   : function(){
            location.reload();
        },error: function(){
            alert("uh ohh");
        }
    })


}