document.addEventListener("DOMContentLoaded", function() {
    var emailInput = document.getElementById('email');
    var emailError = document.getElementById('emailError');    
    var twofaInput = document.getElementById('twofa');
    var twofaError = document.getElementById('twofaError');
    var sendButton = document.getElementById('sendButton');
    sendButton.disabled = true;
    var emailCheck = false;
    var passCheck = false;
    emailInput.addEventListener('blur', function() {
        emailError.textContent = '';
        if (emailInput.value.trim() === '') {
            emailError.textContent = 'Toto pole je povinné.';
            emailCheck = false;
            sendButton.disabled = true;
        } else {
            emailCheck = true;
        }
        
        if(emailCheck == true & passCheck == true) {
            sendButton.disabled = false;
        }

    });
    twofaInput.addEventListener('blur', function() {
        twofaError.textContent = '';
        if (twofaInput.value.trim() === '') {
            twofaError.textContent = 'Toto pole je povinné.';
            passCheck = false;
            sendButton.disabled = true;
        } else {
            passCheck = true;
            if(emailCheck == true) {
                sendButton.disabled = false;
            }
        }
    });
});