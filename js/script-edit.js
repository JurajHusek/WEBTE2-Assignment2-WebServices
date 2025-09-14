$(document).ready(function () {
    const id = $('#edit-id').val();

    fetch(`https://node53.webte.fei.stuba.sk/z1/api/v0/laureates/${id}/details`)
        .then(response => response.json())
        .then(data => {
            if (!Array.isArray(data) || data.length === 0) {
                $('#alert-box').html('<div class="alert alert-danger">Údaje o laureátovi neboli nájdené.</div>');
                return;
            }

            const laureate = data[0];
            const hasFullname = laureate.fullname && laureate.fullname.trim() !== "";

            if (hasFullname) {
                $('#fullname-group').show();
                $('#organisation-group').hide();
                $('#edit-fullname').val(laureate.fullname);
            } else {
                $('#fullname-group').hide();
                $('#organisation-group').show();
                $('#edit-organisation').val(laureate.organisation || '');
            }

            $('#edit-sex').val(laureate.sex || '');
            $('#edit-birth').val(laureate.birth_year || '');
            $('#edit-death').val(laureate.death_year || '');
        })
        .catch(() => {
            $('#alert-box').html('<div class="alert alert-danger">Chyba pri načítaní údajov.</div>');
        });

    $('#editForm').submit(function (e) {
        e.preventDefault();

        const data = {
            fullname: $('#edit-fullname').val(),
            organisation: $('#edit-organisation').val(),
            sex: $('#edit-sex').val(),
            birth_year: $('#edit-birth').val(),
            death_year: $('#edit-death').val()
        };

        fetch(`https://node53.webte.fei.stuba.sk/z1/api/v0/laureates/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(json => {
            if (json.message === "Updated successfully") {
                $('#alert-box').html('<div class="alert alert-success">Údaje boli úspešne aktualizované.</div>');
            } else {
                $('#alert-box').html(`<div class="alert alert-danger">Chyba: ${json.message}</div>`);
            }
        })
        .catch(() => {
            $('#alert-box').html('<div class="alert alert-danger">Chyba pri ukladaní údajov.</div>');
        });
    });

    $('#deleteBtn').click(function () {
        fetch(`https://node53.webte.fei.stuba.sk/z1/api/v0/laureates/${id}`, {
            method: 'DELETE'
        })
        .then(res => res.json())
        .then(json => {
            if (json.message === "Deleted successfully") {
                window.location.href = "admin-panel.php";
            } else {
                $('#alert-box').html(`<div class="alert alert-danger">Chyba: ${json.message}</div>`);
            }
        })
        .catch(() => {
            $('#alert-box').html('<div class="alert alert-danger">Chyba pri vymazávaní údajov.</div>');
        });
    });
});
