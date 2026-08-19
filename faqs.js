var questions = document.getElementsByClassName("faq-question");

for (var i = 0; i < questions.length; i++) {
    questions[i].onclick = function () {
        toggleFAQ(this);
    };
}

function toggleFAQ(question) {
    var item = question.parentElement;
    var icon = question.getElementsByTagName("span")[0];

    if (item.classList.contains("open")) {
        item.classList.remove("open");
        icon.innerHTML = "+";
    } else {
        item.classList.add("open");
        icon.innerHTML = "-";
    }
}
