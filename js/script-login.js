
document.addEventListener("DOMContentLoaded", function() {
    var emailInput = document.getElementById('email');
    var emailError = document.getElementById('emailError');    
    var passwordInput = document.getElementById('password');
    var passwordError = document.getElementById('passwordError');
    var twoFac = document.getElementById('twoFAContainer');
    var emailCheck = false;
    var passCheck = false;
    emailInput.addEventListener('blur', function() {
        emailError.textContent = '';
        var emailPattern = /^[a-zA-Z0-9._%+-]{3,}@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}(?:\.[a-zA-Z]{2,4})?$/;

        if (!emailPattern.test(emailInput.value)) {
            emailError.textContent = 'Neplatný email';
            twoFac.classList.add("d-none");
            emailCheck = false;
        } else {
            emailCheck = true;
        }

        if (emailInput.value.trim() === '') {
            emailError.textContent = 'Toto pole je povinné.';
            twoFac.classList.add("d-none");
            emailCheck = false;
        }
        
        if(emailCheck == true & passCheck == true) {
            twoFac.classList.remove("d-none");
        }

    });
    passwordInput.addEventListener('blur', function() {
        passwordError.textContent = '';
        if (passwordInput.value.trim() === '') {
            passwordError.textContent = 'Toto pole je povinné.';
            twoFac.classList.add("d-none");
            passCheck = false;
        } else {
            passCheck = true;
            if(emailCheck == true) {
                twoFac.classList.remove("d-none");
            }
        }
    });
});