function addPrize() {
    const container = document.createElement('div');
    container.className = 'prize-group border p-3 mb-3 rounded bg-white';
    container.innerHTML = `
        <div class="mb-2">
            <label>Rok</label>
            <input type="number" class="form-control" name="prize_year">
        </div>
        <div class="mb-2">
            <label>Kategória</label>
            <input type="text" class="form-control category-input" name="prize_category">
        </div>
        <div class="mb-2">
            <label>Prínos (SK)</label>
            <input type="text" class="form-control" name="prize_contrib_sk">
        </div>
        <div class="mb-2">
            <label>Prínos (EN)</label>
            <input type="text" class="form-control" name="prize_contrib_en">
        </div>
        <div class="literature-fields mt-3" style="display: none;">
            <h5>Detaily pre literatúru</h5>
            <div class="mb-2">
                <label>Jazyk (SK)</label>
                <input type="text" class="form-control" name="language_sk">
            </div>
            <div class="mb-2">
                <label>Jazyk (EN)</label>
                <input type="text" class="form-control" name="language_en">
            </div>
            <div class="mb-2">
                <label>Žáner (SK)</label>
                <input type="text" class="form-control" name="genre_sk">
            </div>
            <div class="mb-2">
                <label>Žáner (EN)</label>
                <input type="text" class="form-control" name="genre_en">
            </div>
        </div>
    `;

    container.querySelector(".category-input").addEventListener("input", function () {
        const litBlock = container.querySelector(".literature-fields");
        if (this.value.toLowerCase() === "literatúra") {
            litBlock.style.display = "block";
        } else {
            litBlock.style.display = "none";
        }
    });

    document.getElementById('prizes').appendChild(container);
}

addPrize();

const form = document.getElementById('laureateForm');
form.addEventListener('submit', async function(e) {
    e.preventDefault();

    const prizes = [...document.querySelectorAll('.prize-group')].map(group => {
        return {
            year: group.querySelector('[name=prize_year]').value,
            category: group.querySelector('[name=prize_category]').value,
            contrib_sk: group.querySelector('[name=prize_contrib_sk]').value,
            contrib_en: group.querySelector('[name=prize_contrib_en]').value,
            language_sk: group.querySelector('[name=language_sk]')?.value || null,
            language_en: group.querySelector('[name=language_en]')?.value || null,
            genre_sk: group.querySelector('[name=genre_sk]')?.value || null,
            genre_en: group.querySelector('[name=genre_en]')?.value || null
        };
    });

    const payload = {
        fullname: document.getElementById('fullname').value,
        organisation: document.getElementById('organisation').value,
        sex: document.getElementById('sex').value,
        birth: document.getElementById('birth').value,
        death: document.getElementById('death').value || null,
        country: document.getElementById('country').value,
        prizes: prizes
    };

    const responseBox = document.getElementById('response');
    responseBox.innerHTML = "<div class='alert alert-info'>Odosielam...</div>";

    const res = await fetch('https://node53.webte.fei.stuba.sk/z1/api/v0/laureates', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
    });

    const result = await res.json();
    responseBox.innerHTML = res.ok
        ? `<div class='alert alert-success'>${result.message}</div>`
        : `<div class='alert alert-danger'>Chyba: ${result.message || result.error}</div>`;
});