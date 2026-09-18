const adminProfilesContainer = document.getElementById('adminProfilesList');

if (adminProfilesContainer) {
  fetch('data/adminProfiles.json')
    .then(response => {
      if (!response.ok) {
        throw new Error('Failed to load admin profiles data');
      }
      return response.json();
    })
    .then(admins => {
      adminProfilesContainer.innerHTML = admins.map(admin => `
        <div class="profile-card">
          <h3>${admin.name}</h3>
          <p><strong>Role:</strong> ${admin.role}</p>
          <p><strong>Department:</strong> ${admin.department}</p>
          <p><strong>Email:</strong> ${admin.email}</p>
          <p><strong>Phone:</strong> ${admin.phone}</p>
          <p><strong>Status:</strong> ${admin.status}</p>
        </div>
      `).join('');
    })
    .catch(error => {
      console.error('Error loading admin profiles:', error);
      adminProfilesContainer.innerHTML = '<p>Unable to load admin data right now.</p>';
    });
}
