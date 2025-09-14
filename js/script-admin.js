$(document).ready(function () {
    loadFiltersFromAPI();

    var table = $('#nobelTable').DataTable({
        ajax: {
            url: "https://node53.webte.fei.stuba.sk/z1/api/v0/laureates/prizes",
            dataSrc: ""
        },
        columns: [
            {
                data: null,
                render: function (data, type, row) {
                    return row.fullname && row.fullname.trim() !== ''
                        ? row.fullname
                        : (row.organisation);
                }
            },
            { data: "year" },
            { data: "category" },
            { data: "country_name" },
            {
                data: "id",
                render: function (data) {
                    return `<a href="edit.php?id=${data}" class="btn btn-warning btn-sm">Upraviť</a>`;
                }
            }
        ],
        pageLength: 10,
        lengthMenu: [[10, 20, -1], [10, 20, "Všetkých"]],
        language: {
            lengthMenu: "Zobraziť _MENU_ nobelistov na stránku",
            zeroRecords: "Žiadne záznamy neboli nájdené",
            info: "Zobrazené _START_ až _END_ z _TOTAL_ záznamov",
            infoEmpty: "Žiadne záznamy na zobrazenie",
            infoFiltered: "(filtrované z _MAX_ celkových záznamov)",
            search: "Hľadať:",
            paginate: {
                first: "Prvá",
                last: "Posledná",
                next: "Ďalšia",
                previous: "Predošlá"
            }
        }
    });
    function applyFiltersAndReload() {
        const year = $('#yearFilter').val();
        const category = $('#categoryFilter').val();
        const country = $('#countryFilter').val(); // ak ho máš
    
        let url = 'https://node53.webte.fei.stuba.sk/z1/api/v0/laureates/prizes-filtered';
        const params = new URLSearchParams();
    
        if (year) params.append('year', year);
        if (category) params.append('category', category);
        if (country) params.append('country', country);
    
        table.ajax.url(`${url}?${params.toString()}`).load();
    }
    
    $('#yearFilter, #categoryFilter, #countryFilter').change(function () {
        applyFiltersAndReload();
    });
    // Nová funkcia na filtrovanie cez API
    function reloadWithFilters() {
        const year = $('#yearFilter').val();
        const category = $('#categoryFilter').val();

        const url = new URL("https://node53.webte.fei.stuba.sk/z1/api/v0/laureates/filter");
        if (year) url.searchParams.append("year", year);
        if (category) url.searchParams.append("category", category);
        const finalUrl = url.toString();

        table.clear().draw();
        $.get(finalUrl, function (data) {
            table.rows.add(data).draw();
        });
    }


    function loadFiltersFromAPI() {
        fetch("https://node53.webte.fei.stuba.sk/z1/api/v0/laureates/filters")
            .then(response => response.json())
            .then(data => {
                const yearSelect = $('#yearFilter');
                const categorySelect = $('#categoryFilter');

                yearSelect.empty().append('<option value="">Všetky</option>');
                data.years.forEach(year => yearSelect.append(`<option value="${year}">${year}</option>`));

                categorySelect.empty().append('<option value="">Všetky</option>');
                data.categories.forEach(cat => categorySelect.append(`<option value="${cat}">${cat}</option>`));
            })
            .catch(err => console.error("Chyba pri fíltroch:", err));
    }
});


document.getElementById('uploadForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const fileInput = document.getElementById('jsonFile');
    const resultBox = document.getElementById('uploadResult');

    if (!fileInput.files.length) return;

    const file = fileInput.files[0];
    const text = await file.text();

    let data;
    try {
        data = JSON.parse(text);
    } catch (err) {
        resultBox.innerHTML = '<div class="alert alert-danger">Neplatný JSON súbor</div>';
        return;
    }

    if (!Array.isArray(data)) {
        resultBox.innerHTML = '<div class="alert alert-danger">JSON musí obsahovať pole objektov</div>';
        return;
    }

    resultBox.innerHTML = '<div class="alert alert-info">Spracúvam záznamy...</div>';

    let success = 0;
    let failed = 0;

    for (const item of data) {
        try {
            const res = await fetch('https://node53.webte.fei.stuba.sk/z1/api/v0/laureates', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(item)
            });

            if (res.ok) {
                success++;
            } else {
                failed++;
                const result = await res.json();
                console.error("Chyba pri vkladaní:", result);
            }
        } catch (err) {
            failed++;
            console.error("Výnimka pri vkladaní:", err);
        }
    }

    resultBox.innerHTML = `
        <div class="alert alert-success">Úspešne pridaných: ${success}</div>
        ${failed > 0 ? `<div class="alert alert-warning">Zlyhaných: ${failed}</div>` : ''}
    `;
});
$('#newLaureateButton').click(function () {
    window.location.href = 'add-laureate.php';
});



