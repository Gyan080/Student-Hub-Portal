var form = document.querySelector("form");

form.onsubmit = function (event) {
    event.preventDefault();
    showPopup("Student login successful!");
};

function showPopup(message) {
    var popup = document.createElement("div");
    popup.innerHTML = "<div style='background:white; padding:25px; text-align:center; border-radius:8px;'><p>" + message + "</p><button onclick='this.parentElement.parentElement.remove()'>OK</button></div>";
    popup.style = "position:fixed; inset:0; background:rgba(0,0,0,0.5); display:flex; align-items:center; justify-content:center;";
    document.body.appendChild(popup);
}