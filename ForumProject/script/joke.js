function joke() {
    var body = document.getElementById("window");
    body.setAttribute("style", "display:none");
    var icon = document.getElementById("joke");
    icon.setAttribute("style", "display:flex");
    icon.addEventListener("dblclick", reversejoke);
}
function reversejoke() {
    var icon = document.getElementById("joke");
    icon.setAttribute("style", "display:none");
    var body = document.getElementById("window");
    body.setAttribute("style", "");

}