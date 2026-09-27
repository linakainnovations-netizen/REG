/* Announcements Page Scripts */
document.addEventListener('DOMContentLoaded', function() {
    const newBtn = document.getElementById('newAnnouncementBtn');
    if (newBtn) {
        newBtn.addEventListener('click', function() {
            // Placeholder for modal or redirection to creation page
            alert('Opening Global Announcement Composer...');
        });
    }

    // Logic for Approve/Reject buttons
    document.querySelectorAll('.btn-success[title="Approve"]').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const title = row.querySelector('.font-weight-bold').innerText;
            if (confirm(`Are you sure you want to approve "${title}"?`)) {
                // In a real app, this would be an AJAX call
                row.querySelector('.status-pill').className = 'status-pill status-published';
                row.querySelector('.status-pill').innerText = 'Published';
                this.remove();
            }
        });
    });
});
