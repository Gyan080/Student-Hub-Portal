var monthNames = ["JAN","FEB","MAR","APR","MAY","JUN","JUL","AUG","SEP","OCT","NOV","DEC"];

var PAGE_SIZE = 6;

var allEvents = [];
var currentCategory = "all";
var currentPage = 1;

function getTagClass(category) {
  if (category === "tech") return "tag-navy";
  if (category === "cultural") return "tag-crimson";
  if (category === "sports") return "tag-green";
  return "tag-gold";
}

function getTagLabel(category) {
  if (category === "tech") return "TECHNICAL";
  if (category === "cultural") return "CULTURAL";
  if (category === "sports") return "SPORTS";
  if (category === "workshop") return "WORKSHOP";
  return "OTHER";
}

function buildCard(ev) {
  var parts = ev.date ? ev.date.split("-") : [];
  var day   = parts.length === 3 ? parts[2] : "TBA";
  var mon   = parts.length === 3 ? monthNames[parseInt(parts[1], 10) - 1] : "";
  var year  = parts.length === 3 ? parts[0] : "";

  var article = document.createElement("article");
  article.className = "ev-card";
  article.setAttribute("data-category", ev.category);

  var dtDiv = document.createElement("div");
  dtDiv.className = "ev-dt";

  var daySpan = document.createElement("span");
  daySpan.className = "event-day";
  daySpan.textContent = day;

  var monSpan = document.createElement("span");
  monSpan.className = "event-month";
  monSpan.textContent = mon;

  var yearSpan = document.createElement("span");
  yearSpan.className = "event-year";
  yearSpan.textContent = year;

  dtDiv.appendChild(daySpan);
  dtDiv.appendChild(monSpan);
  dtDiv.appendChild(yearSpan);

  var cntDiv = document.createElement("div");
  cntDiv.className = "ev-cnt";

  var metaDiv = document.createElement("div");
  metaDiv.className = "ev-meta";

  var tag = document.createElement("span");
  tag.className = "ev-tag " + getTagClass(ev.category);
  tag.textContent = getTagLabel(ev.category);

  var venue = document.createElement("span");
  venue.className = "event-venue";
  venue.textContent = ev.venue;

  metaDiv.appendChild(tag);
  metaDiv.appendChild(venue);

  var title = document.createElement("h2");
  title.className = "ev-tit";
  title.textContent = ev.title;

  var desc = document.createElement("p");
  desc.className = "ev-desc";
  desc.textContent = ev.description;

  var foot = document.createElement("div");
  foot.className = "ev-foot";

  var time = document.createElement("span");
  time.className = "ev-tm";
  time.textContent = Number(ev.fee) > 0 ? "Fee: ₹" + ev.fee : "Free";

  var btn = document.createElement("button");
  btn.type = "button";
  btn.className = "btn btn-primary register-event-btn";
  btn.setAttribute("data-event-id", ev.id);
  btn.textContent = "Register Now \u2192";
  if (Number(ev.registered) === 1) {
    btn.textContent = "Registered \u2713";
    btn.classList.add("registered-btn");
    btn.disabled = true;
  }

  btn.addEventListener("click", function () {
    if (btn.disabled) { return; }
    btn.disabled = true;
    var formData = new FormData();
    formData.append("event_id", ev.id);

    fetch("../../Api/student_events.php", { method: "POST", body: formData })
      .then(function (response) {
        return response.json().then(function (data) {
          if (!response.ok) { throw new Error(data.message || "Registration failed."); }
          return data;
        });
      })
      .then(function (data) {
        btn.textContent = "Registered \u2713";
        btn.classList.add("registered-btn");
        alert(data.message);
      })
      .catch(function (error) {
        btn.disabled = false;
        alert(error.message);
      });
  });

  foot.appendChild(time);
  foot.appendChild(btn);

  cntDiv.appendChild(metaDiv);
  cntDiv.appendChild(title);
  cntDiv.appendChild(desc);
  cntDiv.appendChild(foot);

  article.appendChild(dtDiv);
  article.appendChild(cntDiv);

  return article;
}

function getFilteredEvents() {
  var searchText = document.getElementById("eventSearch").value.toLowerCase();
  var sortVal    = document.getElementById("eventSort").value;

  var filtered = [];
  for (var i = 0; i < allEvents.length; i++) {
    var ev = allEvents[i];

    if (currentCategory !== "all" && ev.category !== currentCategory) {
      continue;
    }

    if (searchText !== "") {
      var inTitle = ev.title.toLowerCase().indexOf(searchText) !== -1;
      var inDesc  = ev.description.toLowerCase().indexOf(searchText) !== -1;
      var inVenue = ev.venue.toLowerCase().indexOf(searchText) !== -1;
      if (!inTitle && !inDesc && !inVenue) {
        continue;
      }
    }

    filtered.push(ev);
  }

  var sorted = filtered.slice();
  if (sortVal === "date-asc") {
    sorted.sort(function (a, b) { return a.date > b.date ? 1 : -1; });
  } else if (sortVal === "date-desc") {
    sorted.sort(function (a, b) { return a.date < b.date ? 1 : -1; });
  } else if (sortVal === "title-asc") {
    sorted.sort(function (a, b) { return a.title.toLowerCase() > b.title.toLowerCase() ? 1 : -1; });
  }

  return sorted;
}

function renderPage() {
  var container  = document.getElementById("events-container");
  var countTag   = document.getElementById("eventCount");
  var statusEl   = document.getElementById("eventStatus");
  var pagerEl    = document.getElementById("eventPager");

  container.innerHTML = "";
  pagerEl.innerHTML   = "";
  statusEl.textContent = "";

  var filtered   = getFilteredEvents();
  var totalCount = filtered.length;
  var totalPages = Math.ceil(totalCount / PAGE_SIZE);

  countTag.textContent = totalCount + " Upcoming Events";

  if (totalCount === 0) {
    statusEl.textContent = "No events match your search.";
    return;
  }

  if (currentPage > totalPages) { currentPage = totalPages; }
  if (currentPage < 1) { currentPage = 1; }

  var start  = (currentPage - 1) * PAGE_SIZE;
  var pageEvents = filtered.slice(start, start + PAGE_SIZE);

  for (var i = 0; i < pageEvents.length; i++) {
    container.appendChild(buildCard(pageEvents[i]));
  }

  var prevBtn = document.createElement("button");
  prevBtn.type = "button";
  prevBtn.className = "f-btn";
  prevBtn.textContent = "Previous";
  prevBtn.disabled = (currentPage === 1);
  prevBtn.addEventListener("click", function () {
    currentPage--;
    renderPage();
  });

  var pageInfo = document.createElement("span");
  pageInfo.className = "pager-info";
  pageInfo.textContent = "Page " + currentPage + " of " + totalPages;

  var nextBtn = document.createElement("button");
  nextBtn.type = "button";
  nextBtn.className = "f-btn";
  nextBtn.textContent = "Next";
  nextBtn.disabled = (currentPage === totalPages);
  nextBtn.addEventListener("click", function () {
    currentPage++;
    renderPage();
  });

  pagerEl.appendChild(prevBtn);
  pagerEl.appendChild(pageInfo);
  pagerEl.appendChild(nextBtn);
}

document.addEventListener("DOMContentLoaded", function () {
  var statusEl = document.getElementById("eventStatus");
  statusEl.textContent = "Loading events...";

  fetch("../../Api/student_events.php")
    .then(function (response) {
      if (!response.ok) { throw new Error("Network error"); }
      return response.json();
    })
    .then(function (data) {
      allEvents = data;
      statusEl.textContent = "";
      renderPage();
    })
    .catch(function () {
      statusEl.textContent = "Could not load events.";
    });

  var filterBtns = document.querySelectorAll(".f-btn[data-category]");
  filterBtns.forEach(function (btn) {
    btn.addEventListener("click", function () {
      filterBtns.forEach(function (b) { b.classList.remove("active"); });
      btn.classList.add("active");
      currentCategory = btn.getAttribute("data-category");
      currentPage = 1;
      renderPage();
    });
  });

  document.getElementById("eventSearch").addEventListener("input", function () {
    currentPage = 1;
    renderPage();
  });

  document.getElementById("eventSort").addEventListener("change", function () {
    currentPage = 1;
    renderPage();
  });
});