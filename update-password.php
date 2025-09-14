<?php
session_start();
require_once 'config.php'; 

$errors = "";
$success = "";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"] ?? null;

if (!$user_id) {
    die("Chyba: Nepodarilo sa identifikovať používateľa.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = trim($_POST["new_password"]);
    $confirm_password = trim($_POST["confirm_password"]);
    $errors = "";

    // Kontrola dĺžky hesla
    if (strlen($new_password) < 8) {
        $errors = "Heslo musí mať aspoň 8 znakov!";
    } 
    // Kontrola, či obsahuje aspoň jedno písmeno, číslo a špeciálny znak
    elseif (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[\W_]).{8,}$/", $new_password)) {
        $errors = "Heslo musí obsahovať aspoň jedno písmeno, jedno číslo a jeden špeciálny znak!";
    } 
    // Kontrola, či sa heslá zhodujú
    elseif ($new_password !== $confirm_password) {
        $errors = "Heslá sa nezhodujú!";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Aktualizujeme heslo v DB
        $stmt = $db->prepare("UPDATE users SET password = :password WHERE id = :id");
        $stmt->bindParam(":password", $hashed_password, PDO::PARAM_STR);
        $stmt->bindParam(":id", $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $success = "Heslo bolo úspešne zmenené! <a href='restricted.php'>Dashboard</a>";
        } else {
            $errors = "Chyba pri zmene hesla: " . implode(" ", $stmt->errorInfo());
        }
    }
}
?>


<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>WEBTE2 - Zmena hesla</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
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
                </ul>
            </div>
        </div>
    </nav>  
    <main>
        <?php


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
        <div class="container mt-5">
            <h3 class="text-center">Nastavenie nového hesla</h3>

            <?php if ($errors): ?>
                <p class='alert alert-danger'><?= htmlspecialchars($errors) ?></p>
            <?php endif; ?>

            <?php if ($success): ?>
                <p class='alert alert-success'><?= $success ?></p>
            <?php else: ?>
                <form method="POST">
                    <label for="new_password">Nové heslo:</label>
                    <input type="password" name="new_password" class="form-control" id="new_password" required>
                    <p class="error" id="pswError"></p>
                    <label for="confirm_password">Potvrďte heslo:</label>
                    <input type="password" name="confirm_password" class="form-control" id="confirm_password" required>
                    <p class="error" id="psw2Error"></p>
                    <button type="submit" class="btn btn-primary mt-3" id="submitButton">Resetovať heslo</button>
                </form>
            <?php endif; ?>
        </div>
    </main>
    <footer class="bg-dark text-light pt-4 pb-4">
        <div class="text-center mt-4">
            <p class="mb-0">Všetky práva vyhradené &copy; 2025 Juraj Hušek Zadanie na WEBTE2</p>
        </div>
    </footer>
    <script src="js/script-newpsw.js"></script>   
</body>
</html>
