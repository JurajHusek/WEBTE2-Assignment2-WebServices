<?php
session_start();
require_once "config.php";

$errors = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
    $tfa_code = isset($_POST["2fa"]) ? trim($_POST["2fa"]) : "";

    // Overenie poli
    if (empty($email) || empty($tfa_code)) {
        $errors = "Všetky polia sú povinné.";
    } else {
        // Overenie v db
        $stmt = $db->prepare("SELECT id FROM users WHERE email = :email AND 2fa_code = :tfa_code");
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":tfa_code", $tfa_code, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION["reset_user_id"] = $user["id"];
            header("Location: set-new-password.php");
            exit;
        } else {
            $errors = "Nesprávny e-mail alebo 2FA kód.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>WEBTE2 Zabudnuté heslo</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
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

    <div class="container mt-5">
        <h3 class="text-center">Obnova hesla</h3>
        <?php if ($errors) echo "<p class='alert alert-danger'>$errors</p>"; ?>
        <form method="POST">
            <label for="email">Váš e-mail:</label>
            <input type="email" name="email" class="form-control" id="email" required>
            <p class="error" id="emailError"></p>
            <label for="2fa">2FA kód:</label>
            <input type="text" name="2fa" class="form-control" id="twofa" required>
            <p class="error" id="twofaError"></p>
            <button type="submit" class="btn btn-primary mt-3" id="sendButton">Overiť</button>
        </form>
    </div>
</main>
    <footer class="bg-dark text-light pt-4 pb-4">
        <div class="text-center mt-4">
            <p class="mb-0">Všetky práva vyhradené &copy; 2025 Juraj Hušek Zadanie na WEBTE2</p>
        </div>
    </footer>
    <script src="js/script-reset.js"></script>
</body>
</html>
