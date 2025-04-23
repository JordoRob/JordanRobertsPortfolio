window.onbeforeunload = function(e){
    var pageurl = window.location.pathname.split('?')[0];
    console.log(pageurl);
    //const blob = new Blob([JSON.stringify({pageurl:pageurl})], { type: 'multipart/form-data; charset=UTF-8' });
    navigator.sendBeacon("ajax/page-exit.php?pageurl="+pageurl);
}
