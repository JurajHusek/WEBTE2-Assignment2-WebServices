document.addEventListener("DOMContentLoaded", function() {
    var passwordInput = document.getElementById('new_password');
    var passwordError = document.getElementById('pswError');
    var password2Input = document.getElementById('confirm_password');
    var password2Error = document.getElementById('psw2Error');
    var submitButton = document.getElementById('submitButton');
    passwordInput.addEventListener('blur', function() {
        var password = passwordInput.value;
        passwordError.textContent = '';

        if (password.length < 8) {
            passwordError.textContent = 'Heslo musí mať aspoň 8 znakov.';
        } else if (!/(?=.*[A-Za-z])(?=.*\d)(?=.*[\W_])/.test(password)) {
            passwordError.textContent = 'Heslo musí obsahovať aspoň jedno písmeno, číslo a špeciálny znak.';
        }
        checkFormValidity();
    });

    // Kontrola zhodnosti hesiel pri `blur`
    password2Input.addEventListener('blur', function() {
        password2Error.textContent = '';

        if (passwordInput.value !== password2Input.value) {
            password2Error.textContent = 'Heslá sa nezhodujú.';
        }
        checkFormValidity();
    });

    // Funkcia na kontrolu celkovej validity formulára
    function checkFormValidity() {
        if (
            passwordError.textContent === '' &&
            password2Error.textContent === ''
        ) {
            submitButton.disabled = false;
        } else {
            submitButton.disabled = true;
        }
    }
});