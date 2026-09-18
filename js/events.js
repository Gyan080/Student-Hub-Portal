const eventsContainer = document.getElementById('eventsList');

if (eventsContainer) {
  fetch('data/events.json')
    .then(response => {
      if (!response.ok) {
        throw new Error('Failed to load events data');
      }
      return response.json();
    })
    .then(events => {
      eventsContainer.innerHTML = events.map(event => `
        <div class="event-card">
          <h3>${event.title}</h3>
          <p><strong>Date:</strong> ${event.date}</p>
          <p><strong>Time:</strong> ${event.time}</p>
          <p><strong>Location:</strong> ${event.location}</p>
          <p>${event.description}</p>
        </div>
      `).join('');
    })
    .catch(error => {
      console.error('Error loading events:', error);
      eventsContainer.innerHTML = '<p>Unable to load events right now.</p>';
    });
}
