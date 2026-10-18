var allFaqs = [];
var filteredFaqs = [];
var currentPage = 1;
var perPage = 5;

document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("faqStatus").textContent = "Loading FAQs...";

  fetch("JSON/faqs.json")
    .then(function (response) { return response.json(); })
    .then(function (data) {
      allFaqs = data;
      applySearch();
    })
    .catch(function () {
      document.getElementById("faqStatus").textContent = "Could not load FAQs.";
    });

  document.getElementById("faqSearch").addEventListener("input", function () {
    currentPage = 1;
    applySearch();
  });
});

function applySearch() {
  var text = document.getElementById("faqSearch").value.toLowerCase();
  filteredFaqs = [];
  for (var i = 0; i < allFaqs.length; i++) {
    var item = allFaqs[i];
    if (item.question.toLowerCase().indexOf(text) !== -1 || item.answer.toLowerCase().indexOf(text) !== -1) {
      filteredFaqs.push(item);
    }
  }
  showPage();
}

function showPage() {
  var list = document.getElementById("faqList");
  list.innerHTML = "";
  var totalPages = Math.max(1, Math.ceil(filteredFaqs.length / perPage));
  if (currentPage > totalPages) currentPage = totalPages;
  var start = (currentPage - 1) * perPage;
  var items = filteredFaqs.slice(start, start + perPage);

  if (items.length === 0) {
    document.getElementById("faqStatus").textContent = "No FAQs match your search.";
  } else {
    document.getElementById("faqStatus").textContent = "";
  }

  for (var i = 0; i < items.length; i++) {
    list.appendChild(buildItem(items[i]));
  }
  showPager(totalPages);
}

function buildItem(item) {
  var wrapper = document.createElement("article");
  wrapper.className = "faq-item";

  var button = document.createElement("button");
  button.className = "faq-question";
  button.type = "button";
  button.setAttribute("aria-expanded", "false");

  var questionText = document.createElement("span");
  questionText.textContent = item.question;

  var icon = document.createElement("span");
  icon.className = "faq-icon";
  icon.textContent = "+";

  button.appendChild(questionText);
  button.appendChild(icon);

  var answer = document.createElement("div");
  answer.className = "faq-answer";

  var answerPara = document.createElement("p");
  answerPara.textContent = item.answer;
  answer.appendChild(answerPara);

  // Toggle open/close on click
  button.addEventListener("click", function () {
    var open = wrapper.classList.toggle("open");
    button.setAttribute("aria-expanded", open ? "true" : "false");
    icon.textContent = open ? "\u2212" : "+";
  });

  wrapper.appendChild(button);
  wrapper.appendChild(answer);
  return wrapper;
}

function showPager(totalPages) {
  var pager = document.getElementById("faqPager");
  pager.innerHTML = "";

  var prev = document.createElement("button");
  prev.textContent = "Previous";
  prev.disabled = currentPage === 1;
  prev.addEventListener("click", function () { currentPage--; showPage(); });

  var label = document.createElement("span");
  label.textContent = " Page " + currentPage + " of " + totalPages + " ";

  var next = document.createElement("button");
  next.textContent = "Next";
  next.disabled = currentPage === totalPages;
  next.addEventListener("click", function () { currentPage++; showPage(); });

  pager.appendChild(prev);
  pager.appendChild(label);
  pager.appendChild(next);
}