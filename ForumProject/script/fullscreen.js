function fullscreen(){
    var elem = document.fullscreenElement; 
    if(elem==null)
        openFullscreen();
    else{

        exitFullscreen(elem);
}}
        function openFullscreen() {
            var window =document.getElementById("window");
  if (window.requestFullscreen) {
    window.requestFullscreen();
  } else if (window.mozRequestFullScreen) { /* FIREFOX */
    window.mozRequestFullScreen();
  } else if (window.msRequestFullscreen) { /* IE11 */
    window.msRequestFullscreen();
  }
}
function exitFullscreen() {
  if(document.exitFullscreen) {
    document.exitFullscreen();
  } else if (document.mozCancelFullScreen) {
    document.mozCancelFullScreen();
  } else if (document.webkitExitFullscreen) {
    document.webkitExitFullscreen();
  }}