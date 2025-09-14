<?php
require_once("config.php"); 

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Neplatné ID laureáta.");
}

$laureate_id = intval($_GET['id']);

$api_url = "https://node53.webte.fei.stuba.sk/z1/api/v0/laureates/$laureate_id/details";
$response = file_get_contents($api_url);

// API vracia pole všetkých cien daného laureáta
$data = json_decode($response, true);

if (!$data || isset($data["message"])) {
    die("Laureát s ID $laureate_id neexistuje.");
}

// Vezmeme prvý záznam na všeobecné údaje (meno, pohlavie, atď.)
$laureate = $data[0];
?>

<!doctype html>
<html lang="sk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>WEBTE2 Nobels info</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script-info.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">WEBTE 2 Nobels</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Nobelisti</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="restricted.php">Dashboard</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main>
        <?php

        session_start();

        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true ) {
            // User is not logged in, show the link to login and registration form.
            echo '<div class="alert alert-primary" role="alert">
                    Pre prístup k administrátorským funkciám sa prosím <a href="login.php" class="alert-link">prihláste alebo registrujte</a>.
                    </div>';
            //echo '<div class="login-info-panel"><p>Pre pokračovanie sa prosím <a href="login.php">prihláste</a> alebo sa <a href="register.php">zaregistrujte</a>.</p></div>';
        } else {
            // User is logged in, show a welcome message.
            echo '<div class="alert alert-light" role="alert">
                    Dobrý deň '. $_SESSION['fullname'] . '
                    </div>';
            //echo '<div class="login-info-panel"><h2>Dobrý deň ' . $_SESSION['fullname'] . ' </h2></div>';
            //echo '<a href="restricted.php">Zabezpečená stránka</a>';
        }
        ?> 
        <div class="container">
            <!-- Hlavný nadpis: buď fullname alebo organizácia -->
            <h1 id="fullname"><?= htmlspecialchars($laureate['fullname'] ?? 'Neznáme') ?></h1>
            
            <!-- Skrytá organizácia pre JS -->
            <p id="organisation" style="display: none;"><?= htmlspecialchars($laureate['organisation'] ?? '') ?></p>

            <!-- Základné info o laureátovi -->
            <p id="pohlavie"><strong>Pohlavie:</strong> <?= $laureate['sex'] === 'M' ? 'Muž' : ($laureate['sex'] === 'F' ? 'Žena' : 'Neznáme') ?>
            <p><strong>Rok narodenia:</strong> <?= htmlspecialchars($laureate['birth_year']) ?></p>
            <p id="rok_umrtia"><strong>Rok úmrtia:</strong> <?= htmlspecialchars($laureate['death_year'] ?? '-') ?></p>
            <p><strong>Krajina:</strong> <?= htmlspecialchars($laureate['country']) ?></p>
            
            <!-- Informácie o Nobelovej cene -->
            <h2>Nobelove ceny</h2>
<?php foreach ($data as $prize): ?>
    <div class="border rounded p-3 mb-3 bg-light">
        <p><strong>Rok udelenia:</strong> <?= htmlspecialchars($prize['prize_year']) ?></p>
        <p><strong>Kategória:</strong> <?= htmlspecialchars($prize['category']) ?></p>
        <p><strong>Prínos (SK):</strong> <?= htmlspecialchars($prize['contrib_sk']) ?></p>
        <p><strong>Prínos (EN):</strong> <?= htmlspecialchars($prize['contrib_en']) ?></p>

        <?php if (isset($prize['details'])): ?>
            <div class="mt-2 ms-3">
                <h5>Detaily literárnej ceny:</h5>
                <p><strong>Jazyk (SK):</strong> <?= htmlspecialchars($prize['details']['language_sk']) ?></p>
                <p><strong>Jazyk (EN):</strong> <?= htmlspecialchars($prize['details']['language_en']) ?></p>
                <p><strong>Žáner (SK):</strong> <?= htmlspecialchars($prize['details']['genre_sk']) ?></p>
                <p><strong>Žáner (EN):</strong> <?= htmlspecialchars($prize['details']['genre_en']) ?></p>
            </div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

        </div>
    </main>
    <footer class="bg-dark text-light pt-4 pb-4">
        <div class="text-center mt-4">
            <p class="mb-0">Všetky práva vyhradené &copy; 2025 Juraj Hušek Zadanie na WEBTE2</p>
        </div>
    </footer>
</body>
</html>
