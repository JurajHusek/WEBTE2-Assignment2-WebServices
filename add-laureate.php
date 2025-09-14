<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}
require_once 'config.php';
require_once 'vendor/autoload.php'; ?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pridanie laureáta s cenami</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
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
                    <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="admin-panel.php">Admin panel</a>
                    </li>
                    <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="apidoc.php">API</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main>
    <?php echo '<div class="alert alert-light" role="alert">
                    Dobrý deň '. $_SESSION['fullname'] . '
                    </div>'; ?>
<div class="container mt-5">
    <h2 class="mb-4">Pridaj laureáta a ceny</h2>
    <form id="laureateForm">
        <div class="mb-3">
            <label for="fullname" class="form-label">Meno a priezvisko</label>
            <input type="text" class="form-control" id="fullname" required>
        </div>
        <div class="mb-3">
            <label for="organisation" class="form-label">Organizácia</label>
            <input type="text" class="form-control" id="organisation">
        </div>
        <div class="mb-3">
            <label for="sex" class="form-label">Pohlavie</label>
            <select id="sex" class="form-select" required>
                <option value="M">Muž</option>
                <option value="F">Žena</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="birth" class="form-label">Rok narodenia</label>
            <input type="number" class="form-control" id="birth" required>
        </div>
        <div class="mb-3">
            <label for="death" class="form-label">Rok úmrtia (voliteľné)</label>
            <input type="number" class="form-control" id="death">
        </div>
        <div class="mb-3">
            <label for="country" class="form-label">Krajina</label>
            <input type="text" class="form-control" id="country">
        </div>

        <hr>
        <h4>Ocenenia</h4>
        <div id="prizes"></div>
        <button type="button" class="btn btn-secondary mb-3" onclick="addPrize()">+ Pridať cenu</button>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Odoslať</button>
        </div>
    </form>
    <div id="response" class="mt-3"></div>
</div>

<script src="js/script-add.js"></script>
    <footer class="bg-dark text-light pt-4 pb-4">
        <div class="text-center mt-4">
            <p class="mb-0">Všetky práva vyhradené &copy; 2025 Juraj Hušek Zadanie na WEBTE2</p>
        </div>
    </footer>
</body>
</html>