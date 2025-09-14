$(document).ready(function () {
    var fullname = $("#fullname").text().trim();
    var organisation = $("#organisation").text().trim();
    
    // Ak fullname je prázdne alebo "Neznáme", použijeme organisation
    if (fullname === "" || fullname === "Neznáme") {
        $("#fullname").text(organisation);
    }

    // Ak fullname je prázdne, skryjeme pohlavie a rok úmrtia
    if (fullname === "" || fullname === "Neznáme") {
        $("#pohlavie").hide();
        $("#rok_umrtia").hide();
        $("p:contains('Rok narodenia')").html("<strong>Rok založenia:</strong> " + $("p:contains('Rok narodenia')").text().replace("Rok narodenia: ", ""));
    }
});
