const togglePassword = document.querySelector('#togglePassword');
const password = document.querySelector('#password');
const eyeIcon = document.querySelector('#eyeIcon');
togglePassword.addEventListener('click', function () {
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    if (type === 'text') {
        eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
    }
});


const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
const confirmPassword = document.querySelector('#password_confirmation');
const eyeIconConfirm = document.querySelector('#eyeIconConfirm');

toggleConfirmPassword.addEventListener('click', function () {
    const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
    confirmPassword.setAttribute('type', type);
    if (type === 'text') {
        eyeIconConfirm.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        eyeIconConfirm.classList.replace('fa-eye-slash', 'fa-eye');
    }
});
