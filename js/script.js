$(document).ready(function() {
    var table = $('#nobelTable').DataTable({
        "ajax": {
            "url": "https://node53.webte.fei.stuba.sk/z1/api/v0/laureates/prizes",  // Tvoje REST API
            "dataSrc": ""  // Očakáva pole objektov v root JSONe
        },
        "columns": [
            { "data": "fullname" },
            { "data": "year" },
            { "data": "category" },
            { "data": "country_name" },
            {
                "data": "id",
                "render": function(data, type, row, meta) {
                    return `<a href="person.php?id=${data}" class="btn btn-info btn-sm">Info</a>`;
                }
            }
        ],
        "pageLength": 10,
        "lengthMenu": [[10, 20, -1], [10, 20, "Všetkých"]],
        "language": {
            "lengthMenu": "Zobraziť _MENU_ nobelistov na stránku",
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
        "initComplete": function() {
        updateFilters();
    }
    });

    function updateFilters() {
        var uniqueYears = new Set();
        var uniqueCategories = new Set();
    
        table.rows().every(function() {
            var row = this.data(); // ← je to objekt, nie pole
            var year = row.year?.toString().trim();
            var category = row.category?.trim();
    
            if (year) uniqueYears.add(year);
            if (category) uniqueCategories.add(category);
        });
    
        $('#yearFilter').empty().append('<option value="">Všetky</option>');
        Array.from(uniqueYears).sort().forEach(function(year) {
            $('#yearFilter').append(`<option value="${year}">${year}</option>`);
        });
    
        $('#categoryFilter').empty().append('<option value="">Všetky</option>');
        Array.from(uniqueCategories).sort().forEach(function(category) {
            $('#categoryFilter').append(`<option value="${category}">${category}</option>`);
        });
    }
    

    function toggleColumns() {
        var yearSelected = $('#yearFilter').val();
        var categorySelected = $('#categoryFilter').val();

        // Ak je vybraný konkrétny rok, skryjeme stĺpec 1 (rok), inak ho zobrazíme
        table.column(1).visible(yearSelected === "");

        // Ak je vybraná konkrétna kategória, skryjeme stĺpec 2 (kategória), inak ho zobrazíme
        table.column(2).visible(categorySelected === "");
    }

    // Po zmene počtu záznamov sa len prekreslí tabuľka, filtre zostávajú rovnaké
    $('#entriesSelect').change(function() {
        var value = $(this).val();
        table.page.len(value == "-1" ? -1 : parseInt(value)).draw();
    });

    // Filtrácia podľa roku a kategórie + skrytie stĺpcov
    $('#yearFilter, #categoryFilter').change(function() {
        var year = $('#yearFilter').val();
        var category = $('#categoryFilter').val();

        table.columns(1).search(year).columns(2).search(category).draw();
        toggleColumns();  // Skry alebo zobraz stĺpce
    });

    // Počiatočná aktualizácia filtrov po načítaní
    setTimeout(updateFilters, 500);
});
document.addEventListener("DOMContentLoaded", function () {
    var cookieModal = new bootstrap.Modal(document.getElementById("cookieModal"));

    // Skontrolujeme, či už používateľ súhlasil
    if (!localStorage.getItem("cookiesAccepted")) {
        cookieModal.show(); // Zobrazíme modal
    }

    document.getElementById("acceptCookies").addEventListener("click", function () {
        localStorage.setItem("cookiesAccepted", "true"); // Uložíme informáciu o súhlase
        cookieModal.hide(); // Skryjeme modal
    });
});
