    // Theme Toggle
const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const body = document.body;
    const table = document.querySelector('.table'); // ចាប់យក table

    if (localStorage.getItem('theme') === 'light') {
        applyLightMode();
    }

    themeToggle.addEventListener('click', () => {
        if (body.classList.contains('bg-dark')) {
            applyLightMode();
            localStorage.setItem('theme', 'light');
        } else {
            applyDarkMode();
            localStorage.setItem('theme', 'dark');
        }
    });

    function applyLightMode() {
        body.classList.replace('bg-dark', 'bg-light');
        body.classList.replace('text-white', 'text-dark');

        if(table) {
            table.classList.remove('table-dark');
            table.classList.add('table-light', 'border-secondary');
        }

        themeIcon.classList.replace('fa-moon', 'fa-sun');
        themeIcon.classList.add('text-warning');
    }

    function applyDarkMode() {
        body.classList.replace('bg-light', 'bg-dark');
        body.classList.replace('text-dark', 'text-white');


        if(table) {
            table.classList.add('table-dark');
            table.classList.remove('table-light', 'border-secondary');
        }

        themeIcon.classList.replace('fa-sun', 'fa-moon');
        themeIcon.classList.remove('text-warning');
    }

    //Delete Confirmation Modal

    function confirmDelete(userId, userName) {
            // បង្ហាញឈ្មោះក្នុង Modal
            document.getElementById('deleteUserName').innerText = userName;

            // ប្តូរ Form Action ទៅតាម ID របស់ User
            const form = document.getElementById('deleteForm');
            form.action = `/admin/users/${userId}`;

            // បើក Modal
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }
