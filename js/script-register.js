document.addEventListener("DOMContentLoaded", function() {
    var firstnameInput = document.getElementById('firstname');
    var lastnameInput = document.getElementById('lastname');
    var firstnameError = document.getElementById('firstnameError');
    var lastnameError = document.getElementById('lastnameError');
    var emailInput = document.getElementById('email');
    var emailError = document.getElementById('emailError');
    var passwordInput = document.getElementById('password');
    var passwordError = document.getElementById('passwordError');
    var password2Input = document.getElementById('password2');
    var password2Error = document.getElementById('password2Error');
    var submitButton = document.getElementById('registerButton');

    var namePattern = /^[\p{L}\s-]+$/u;
    var emailPattern = /^[a-zA-Z0-9._%+-]{3,}@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}(?:\.[a-zA-Z]{2,4})?$/;

    // Validácia mena pri `blur`
    firstnameInput.addEventListener('blur', function() {
        validateName();
    });

    // Validácia priezviska pri `blur`
    lastnameInput.addEventListener('blur', function() {
        validateName();
    });

    function validateName() {
        var firstname = firstnameInput.value.trim();
        var lastname = lastnameInput.value.trim();
        firstnameError.textContent = '';
        lastnameError.textContent = '';

        if (firstname === '') {
            firstnameError.textContent = 'Meno je povinné.';
        } else if (!namePattern.test(firstname)) {
            firstnameError.textContent = 'Meno obsahuje nepovolené znaky.';
        }

        if (lastname === '') {
            lastnameError.textContent = 'Priezvisko je povinné.';
        } else if (!namePattern.test(lastname)) {
            lastnameError.textContent = 'Priezvisko obsahuje nepovolené znaky.';
        }

        // Kontrola celkovej dĺžky (meno + priezvisko)
        if ((firstname.length + lastname.length) > 128) {
            lastnameError.textContent = 'Meno a priezvisko dohromady nesmie presiahnuť 128 znakov.';
        }

        checkFormValidity();
    }

    // Validácia emailu pri `blur`
    emailInput.addEventListener('blur', function() {
        var email = emailInput.value.trim();
        emailError.textContent = '';

        if (email === '') {
            emailError.textContent = 'E-mail je povinný.';
        } else if (!emailPattern.test(email)) {
            emailError.textContent = 'Neplatný formát e-mailu.';
        }
        checkFormValidity();
    });

    // Validácia hesla pri `blur`
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
            firstnameError.textContent === '' &&
            lastnameError.textContent === '' &&
            emailError.textContent === '' &&
            passwordError.textContent === '' &&
            password2Error.textContent === ''
        ) {
            submitButton.disabled = false;
        } else {
            submitButton.disabled = true;
        }
    }
});
