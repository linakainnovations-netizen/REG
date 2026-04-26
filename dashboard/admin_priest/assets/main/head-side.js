/* Dashboard Shared Layout Scripts */
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.dashboard-sidebar-premium');
    
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('active');
            
            // Change icon
            const icon = menuToggle.querySelector('i');
            if (icon) {
                if (sidebar.classList.contains('active')) {
                    icon.classList.replace('fa-indent', 'fa-outdent');
                } else {
                    icon.classList.replace('fa-outdent', 'fa-indent');
                }
            }
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (sidebar.classList.contains('active') && !sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                sidebar.classList.remove('active');
                const icon = menuToggle.querySelector('i');
                if (icon) icon.classList.replace('fa-outdent', 'fa-indent');
            }
        });
    }
});
