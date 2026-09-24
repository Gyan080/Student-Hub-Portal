document.addEventListener("DOMContentLoaded", function () {
  const faqList = document.getElementById("faqList");
  if (!faqList) return;

  faqList.addEventListener("click", function (event) {
    const questionBtn = event.target.closest(".faq-question");
    if (!questionBtn) return;

    const faqItem = questionBtn.closest(".faq-item");
    if (!faqItem) return;

    const isOpen = faqItem.classList.contains("open");
    const icon = questionBtn.querySelector(".faq-icon");

    if (isOpen) {
      faqItem.classList.remove("open");
      questionBtn.setAttribute("aria-expanded", "false");
      if (icon) icon.textContent = "+";
    } else {
      faqItem.classList.add("open");
      questionBtn.setAttribute("aria-expanded", "true");
      if (icon) icon.textContent = "−";
    }
  });
});

