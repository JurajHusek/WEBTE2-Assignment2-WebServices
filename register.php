<?php

session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit;
}

require_once 'config.php';
require_once 'vendor/autoload.php';
require_once 'utilities.php';

use RobThree\Auth\Providers\Qr\BaconQrCodeProvider;
//use RobThree\Auth\Providers\Qr\EndroidQrCodeProvider;
use RobThree\Auth\TwoFactorAuth;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = "";

    // Validate email DONE
    if (isEmpty($_POST['email']) === true) {
        $errors .= "Nevyplnený e-mail.\n";
    } else {
        $email = $_POST['email'];
        
        // Regex pattern ako mam aj v js
        $emailPattern = "/^[a-zA-Z0-9._%+-]{3,}@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}(?:\.[a-zA-Z]{2,4})?$/";
    
        // controla formátu pomocou filter_var()
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match($emailPattern, $email)) {
            $errors .= "Neplatný formát e-mailu.\n";
        }
    }

    // TODO: validate if user entered correct e-mail format DONE 

    // Validate user existence DONE 
    if (userExist($db, $_POST['email']) === true) {
        $errors .= "Používateľ s týmto e-mailom už existuje.\n";
        die();
    }

   // Validate first name and last name 
    if (empty($_POST['firstname'])) {
        $errors .= "Nevyplnené meno.\n";
    } elseif (empty($_POST['lastname'])) {
        $errors .= "Nevyplnené priezvisko.\n";
    } else {
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];

        // Spočítať celkovú dĺžku mena a priezviska lebo fullname ma max 180 charov
        $fullLength = mb_strlen($firstname, 'UTF-8') + mb_strlen($lastname, 'UTF-8');

        if ($fullLength > 128) {
            $errors .= "Meno a priezvisko dohromady nesmie presiahnuť 128 znakov.\n";
        }

        // Regex pre povolené znaky (iba písmená + diakritika)
        $namePattern = "/^[\p{L}\s-]+$/u"; 

        if (!preg_match($namePattern, $firstname)) {
            $errors .= "Meno obsahuje nepovolené znaky. Povolené sú iba písmená a medzery.\n";
        }

        if (!preg_match($namePattern, $lastname)) {
            $errors .= "Priezvisko obsahuje nepovolené znaky. Povolené sú iba písmená a medzery.\n";
        }
    }


    // Validate password
    if (empty($_POST['password'])) {
        $errors .= "Nevyplnené heslo.\n";
    } elseif (empty($_POST['password2'])) {
        $errors .= "Zopakované heslo nebolo vyplnené.\n";
    } else {
        $password = $_POST['password'];
        $password2 = $_POST['password2'];

        // minimálna dlzka hesla je 8
        if (strlen($password) < 8) {
            $errors .= "Heslo musí mať aspoň 8 znakov.\n";
        }

        // aspoň jedno písmeno, jedno číslo a jeden špeciálny znak)
        $passwordPattern = "/^(?=.*[A-Za-z])(?=.*\d)(?=.*[\W_]).{8,}$/";

        if (!preg_match($passwordPattern, $password)) {
            $errors .= "Heslo musí obsahovať aspoň jedno písmeno, jedno číslo a jeden špeciálny znak.\n";
        }

        // overenie hesiel
        if ($password !== $password2) {
            $errors .= "Heslá sa nezhodujú.\n";
        }
    }

    // TODO: Sanitize and validate all user inputs. DONE in JS

    if (empty($errors)) {
        $sql = "INSERT INTO users (fullname, email, password, 2fa_code) VALUES (:fullname, :email, :password, :2fa_code)";

        $fullname = $_POST['firstname'] . ' ' . $_POST['lastname'];
        $email = $_POST['email'];
        $pw_hash = password_hash($_POST['password'], PASSWORD_ARGON2ID);

        $tfa = new TwoFactorAuth(new BaconQrCodeProvider(4, '#ffffff', '#000000', 'svg'));
        //$tfa = new TwoFactorAuth(new EndroidQrCodeProvider());
        $user_secret = $tfa->createSecret();
        $qr_code = $tfa->getQRCodeImageAsDataUri('Nobels', $user_secret);

        // Bind parameters to SQL
        $stmt = $db->prepare($sql);

        $stmt->bindParam(":fullname", $fullname, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":password", $pw_hash, PDO::PARAM_STR);
        $stmt->bindParam(":2fa_code", $user_secret, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $reg_status = "Registracia prebehla uspesne.";
        } else {
            $reg_status = "Ups. Nieco sa pokazilo...";
        }

        unset($stmt);
    }
    unset($db);
}

?>

<!DOCTYPE html>
<html lang="sk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>WEBTE2 Nobels - Registrácia</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
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
        <div class="container d-flex justify-content-center align-items-center vh-100">
            <div class="card p-4 shadow" style="width: 400px;">
                <h3 class="text-center mb-3">Registrácia</h3>
                
                <?php if (isset($reg_status)): ?>
                    <div class="alert alert-info text-center">
                        <strong><?= htmlspecialchars($reg_status) ?></strong>
                    </div>
                <?php endif; ?>

                <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                    <!-- TODO: Error messages should be separated for corresponding input and must contain meaningful explanations DONE -->
                    
                    <div class="mb-3">
                        <label for="firstname" class="form-label">Meno:</label>
                        <input type="text" name="firstname" class="form-control" id="firstname" placeholder="Meno..." required>
                        <p class="error" id="firstnameError"></p>
                    </div>

                    <div class="mb-3">
                        <label for="lastname" class="form-label">Priezvisko:</label>
                        <input type="text" name="lastname" class="form-control" id="lastname" placeholder="Priezvisko..." required>
                        <p class="error" id="lastnameError"></p>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail:</label>
                        <input type="email" name="email" class="form-control" id="email" placeholder="example@email.com" required>
                        <p class="error" id="emailError"></p>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Heslo:</label>
                        <input type="password" name="password" class="form-control" id="password" required>
                        <p class="error" id="passwordError"></p>
                    </div>
                    <div class="mb-3">
                        <label for="passwordCheck" class="form-label">Zopakujte heslo:</label>
                        <input type="password" name="password2" class="form-control" id="password2" required>
                        <p class="error" id="password2Error"></p>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" id="registerButton">Vytvoriť konto</button>
                </form>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger mt-3">
                        <strong>Chyby:</strong><br>
                        <?= nl2br(htmlspecialchars($errors)) ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($qr_code)): ?>
                    <!-- Modal -->
                    <div class="modal fade show d-block" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="qrModalLabel">Nastavenie 2FA</h5>
                                </div>
                                <div class="modal-body text-center">
                                    <p>Zadajte kód: <strong><?= htmlspecialchars($user_secret) ?></strong> do aplikácie pre 2FA.</p>
                                    <p>alebo naskenujte QR kód:</p>
                                    <img src="<?= htmlspecialchars($qr_code) ?>" alt="QR kód pre aplikáciu authenticator" class="img-fluid">
                                </div>
                                <div class="modal-footer">
                                    <a href="login.php" class="btn btn-primary">Prejsť na prihlásenie</a>
                                </div>
                            </div>
                        </div>
                    </div>

        <!-- Bootstrap modal backdrop to prevent user from interacting with the page -->
        <div class="modal-backdrop fade show"></div>
            <?php endif; ?>
                <hr>
                <div class="text-center">
                    <p>Už máte vytvorené konto? <a href="login.php" class="text-decoration-none">Prihláste sa tu.</a></p>
                </div>
        </div>
    </main>

    <footer class="bg-dark text-light pt-4 pb-4">
        <div class="text-center mt-4">
            <p class="mb-0">Všetky práva vyhradené &copy; 2025 Juraj Hušek Zadanie na WEBTE2</p>
        </div>
    </footer>
    <script src="js/script-register.js"></script>                 
</body>
</html>
