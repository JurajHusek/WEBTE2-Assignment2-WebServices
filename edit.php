<?php
require_once("config.php");
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Neplatné ID.");
}

$id = intval($_GET['id']);
?>

<!doctype html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>Upraviť laureáta</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">WEBTE 2 Nobels</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" href="index.php">Nobelisti</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="restricted.php">Dashboard</a>
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
        <?php echo '<div class="alert alert-light" role="alert">
                    Dobrý deň '. $_SESSION['fullname'] . '
                    </div>'; ?>
        <main>
<div class="container mt-5">
    <h1 class="mb-4">Upraviť laureáta</h1>

    <div id="alert-box"></div>

    <form id="editForm">
        <input type="hidden" id="edit-id" value="<?= $id ?>">

        <div class="mb-3" id="fullname-group">
            <label class="form-label">Meno</label>
            <input type="text" id="edit-fullname" class="form-control" required>
        </div>

        <div class="mb-3" id="organisation-group">
            <label class="form-label">Organizácia</label>
            <input type="text" id="edit-organisation" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Pohlavie (M/F)</label>
            <input type="text" id="edit-sex" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Rok narodenia</label>
            <input type="number" id="edit-birth" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Rok úmrtia</label>
            <input type="number" id="edit-death" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Uložiť zmeny</button>
        <a href="admin-panel.php" class="btn btn-secondary">Zrušiť</a>
        <button type="button" class="btn btn-danger float-end" id="deleteBtn">Vymazať laureáta</button>
    </form>
</div>

</main>
<footer class="bg-dark text-light pt-4 pb-4">
        <div class="text-center mt-4">
            <p class="mb-0">Všetky práva vyhradené &copy; 2025 Juraj Hušek Zadanie na WEBTE2</p>
        </div>
    </footer>
    <script src="js/script-edit.js"></script>
</body>
</html>
