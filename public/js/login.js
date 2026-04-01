    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const eyeIcon = document.querySelector('#eyeIcon');

    togglePassword.addEventListener('click', function (e) {
        // ប្តូរប្រភេទ type រវាង password និង text
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);

        if (type === 'password') {
            eyeIcon.classList.remove('fa','fa-eye-slash');
            eyeIcon.classList.add('fa','fa-eye');
        } else {
            eyeIcon.classList.remove('fa','fa-eye');
            eyeIcon.classList.add('fa','fa-eye-slash');
        }
    });

