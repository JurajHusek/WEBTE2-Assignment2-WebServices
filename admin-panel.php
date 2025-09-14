<?php

session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}

require_once 'config.php';
require_once 'vendor/autoload.php';
?>
<!doctype html>
<html lang="sk">

<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>WEBTE2 Nobels - Admin Panel</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
</head>

<body>
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
    <main>
        <?php echo '<div class="alert alert-light" role="alert">
                    Dobrý deň '. $_SESSION['fullname'] . '
                    </div>'; ?>
        <div class="container mt-5">
            <h3>Pridaj laureáta</h3>
            <button type="button" class="btn btn-primary" id="newLaureateButton">Nový laureát</button>
            <br><br>
            <h3>JSON nahratie</h3>
            <form id="uploadForm">
                <input type="file" id="jsonFile" class="form-control mb-2" accept=".json" required>
                <button type="submit" class="btn btn-primary">Nahrať a spracovať</button>
            </form>
            <div id="uploadResult" class="mt-3"></div>
        </div>

        <div class="datatable container">
            <h1 class="text-center">Admin panel nobelistov</h1>
        
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="yearFilter" class="form-label">Rok:</label>
                    <select id="yearFilter" class="form-select">
                        <option value="">Všetky</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="categoryFilter" class="form-label">Kategória:</label>
                    <select id="categoryFilter" class="form-select">
                        <option value="">Všetky</option>
                    </select>
                </div>
            </div>
            <table id="nobelTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Meno</th>
                        <th>Rok</th>
                        <th>Kategória</th>
                        <th>Krajina</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>


    </main>
    <footer class="bg-dark text-light pt-4 pb-4">
        <div class="text-center mt-4">
            <p class="mb-0">Všetky práva vyhradené &copy; 2025 Juraj Hušek Zadanie na WEBTE2</p>
        </div>
    </footer>
    <script src="js/script-admin.js"></script>
</body>
</html>