$(document).ready(function() {
    $('#loginHistoryTable').DataTable({
        "language": {
            "lengthMenu": "Zobraziť _MENU_ záznamov na stránku",
            "zeroRecords": "Žiadne záznamy neboli nájdené",
            "info": "Zobrazené _START_ až _END_ z _TOTAL_ záznamov",
            "infoEmpty": "Žiadne záznamy na zobrazenie",
            "infoFiltered": "(filtrované z _MAX_ celkových záznamov)",
            "search": "Hľadať:",
            "paginate": {
                "first": "Prvá",
                "last": "Posledná",
                "next": "Ďalšia",
                "previous": "Predošlá"
            }
        },
        "order": [[0, "desc"]],
        "pageLength": 10
    });
});
document.addEventListener("DOMContentLoaded", function() {
    var button = document.getElementById("changeDetailsButton");
    var detailsDiv = document.getElementById("changeDetails");
    var namePattern = /^[\p{L}\s-]+$/u;
    var firstnameInput = document.getElementById('firstname');
    var lastnameInput = document.getElementById('lastname');
    var firstnameError = document.getElementById('firstnameError');
    var lastnameError = document.getElementById('lastnameError');

    button.addEventListener("click", function() {
        if (detailsDiv.hidden) {
            detailsDiv.hidden = false;  
        } else {
            detailsDiv.hidden = true;   
        }
    });
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
    }
});

