const faqContainer = document.getElementById('faqList');

if (faqContainer) {
  fetch('data/faqs.json')
    .then(response => {
      if (!response.ok) {
        throw new Error('Failed to load FAQs data');
      }
      return response.json();
    })
    .then(faqs => {
      faqContainer.innerHTML = faqs.map(faq => `
        <div class="faq-item">
          <h3>Q: ${faq.question}</h3>
          <p>A: ${faq.answer}</p>
        </div>
      `).join('');
    })
    .catch(error => {
      console.error('Error loading FAQs:', error);
      faqContainer.innerHTML = '<p>Unable to load FAQs right now.</p>';
    });
}
