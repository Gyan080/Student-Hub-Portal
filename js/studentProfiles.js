const studentProfilesContainer = document.getElementById('studentProfilesList');

if (studentProfilesContainer) {
  fetch('data/studentProfiles.json')
    .then(response => {
      if (!response.ok) {
        throw new Error('Failed to load student profiles data');
      }
      return response.json();
    })
    .then(students => {
      studentProfilesContainer.innerHTML = students.map(student => `
        <div class="profile-card">
          <h3>${student.name}</h3>
          <p><strong>Student ID:</strong> ${student.studentId}</p>
          <p><strong>Course:</strong> ${student.course}</p>
          <p><strong>Year:</strong> ${student.year}</p>
          <p><strong>Email:</strong> ${student.email}</p>
          <p><strong>Status:</strong> ${student.status}</p>
        </div>
      `).join('');
    })
    .catch(error => {
      console.error('Error loading student profiles:', error);
      studentProfilesContainer.innerHTML = '<p>Unable to load student data right now.</p>';
    });
}
