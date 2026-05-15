// Profile dropdown and theme functionality
(function() {
    var profileButton = document.querySelector('.profile-button');
    var dropdownMenu = document.querySelector('.dropdown-menu');
    var themeToggle = document.getElementById('themeToggle');
    var savedTheme = localStorage.getItem('inventoryTheme') || 'light';

    function applyTheme(theme) {
        if (theme === 'dark') {
            document.body.classList.add('dark-theme');
            if (themeToggle) themeToggle.textContent = '☀️';
        } else {
            document.body.classList.remove('dark-theme');
            if (themeToggle) themeToggle.textContent = '🌙';
        }
        localStorage.setItem('inventoryTheme', theme);
    }

    applyTheme(savedTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            applyTheme(document.body.classList.contains('dark-theme') ? 'light' : 'dark');
        });
    }

    if (!profileButton || !dropdownMenu) {
        return;
    }

    profileButton.addEventListener('click', function(event) {
        event.stopPropagation();
        dropdownMenu.classList.toggle('open');
        var expanded = dropdownMenu.classList.contains('open');
        profileButton.setAttribute('aria-expanded', expanded);
    });

    document.addEventListener('click', function(event) {
        if (!dropdownMenu.contains(event.target) && !profileButton.contains(event.target)) {
            dropdownMenu.classList.remove('open');
            profileButton.setAttribute('aria-expanded', 'false');
        }
    });
})();