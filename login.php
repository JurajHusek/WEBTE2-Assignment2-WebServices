<?php

session_start();

// Check if the user is already logged in, if yes then redirect him to restricted page.
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: restricted.php");
    exit;
}

require_once "config.php";
require_once 'vendor/autoload.php';
require_once 'utilities.php';

use RobThree\Auth\Providers\Qr\EndroidQrCodeProvider;
use RobThree\Auth\TwoFactorAuth;

// Redirect users to outh2call.php which redirects users to Google OAuth 2.0
$redirect_uri = "https://node53.webte.fei.stuba.sk/z1/oauth2callback.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // TODO: Implement login credentials verification. DONE
    // TODO: Implement a mechanism to save login information - user_id, login_type, email, fullname - to database. DONE

    $sql = "SELECT id, fullname, email, password, 2fa_code, created_at FROM users WHERE email = :email";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(":email", $_POST["email"], PDO::PARAM_STR);
    $errors = "";

    if ($stmt->execute()) {
        if ($stmt->rowCount() == 1) {
            // User exists, check password.
            $row = $stmt->fetch();
            $hashed_password = $row["password"];

            if (password_verify($_POST['password'], $hashed_password)) {
                // Password is correct.
                $tfa = new TwoFactorAuth(new EndroidQrCodeProvider());
                if ($tfa->verifyCode($row["2fa_code"], $_POST['2fa'], 2)) {
                    // Password and code are correct, user authenticated.

                    // Save user data to session.
                    $_SESSION["user_id"] = $row['id'];  // Uloženie ID používateľa do session
                    $_SESSION["loggedin"] = true;
                    $_SESSION["fullname"] = $row['fullname'];
                    $_SESSION["email"] = $row['email'];
                    $_SESSION["created_at"] = $row['created_at'];
                    
                    // uozenie prihlasenia do db
                    $login_type = "local"; // Ak je to lokálne prihlásenie (Google má iný typ)
                    $stmt = $db->prepare("
                        INSERT INTO users_login (user_id, login_type, email, fullname, login_time) 
                        VALUES (:user_id, :login_type, :email, :fullname, NOW())"
                    );
                    $stmt->bindParam(":user_id", $row['id'], PDO::PARAM_INT);
                    $stmt->bindParam(":login_type", $login_type, PDO::PARAM_STR);
                    $stmt->bindParam(":email", $row['email'], PDO::PARAM_STR);
                    $stmt->bindParam(":fullname", $row['fullname'], PDO::PARAM_STR);
                    $stmt->execute();

                    // Redirect user to restricted page.
                    header("location: restricted.php");
                }
                else {
                    $errors = "Neplatný kod 2FA.";
                }
            } else {
                $errors = "Nesprávne meno alebo heslo.";
            }
        } else {
            $errors = "Nesprávne meno alebo heslo.";
        }
    } else {
        $errors = "Ups. Niečo sa pokazilo...";
    }

    unset($stmt);
    unset($db);
}

?>

<!doctype html>
<html lang="sk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>WEBTE2 Nobels Login</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
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
        <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow" style="width: 400px;">
            <h3 class="text-center mb-3">Prihlásenie</h3>

            <?php 
            // TODO: Handle login errors DONE
            if (isset($errors)): ?>
                <div class="alert alert-danger text-center">
                    <strong><?= htmlspecialchars($errors) ?></strong>
                </div>
            <?php endif; ?>

            <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">

                <div class="mb-3">
                    <label for="email" class="form-label">E-Mail:</label>
                    <input type="email" name="email" class="form-control" id="email" required>
                    <p id="emailError" class="error"></p>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Heslo:</label>
                    <input type="password" name="password" class="form-control" id="password" required>
                    <p id="passwordError" class="error"></p>
                </div>

                <!-- TODO: Use JavaScript to hide/show the 2FA field after successful password enter,
                            and only after completing the 2FA code, the user is logged in. DONE -->
                <div class="mb-3 d-none" id="twoFAContainer">
                    <label for="2fa" class="form-label">2FA kód:</label>
                    <input type="text" name="2fa" class="form-control" id="2fa" required>
                </div>

                <button type="submit" class="btn btn-primary w-100" id="loginButton">Prihlásiť sa</button>
            </form>

            <hr>

            <div class="text-center">
                <p>Alebo sa prihláste pomocou</p>
                <a href="<?= filter_var($redirect_uri, FILTER_SANITIZE_URL) ?>" class="btn btn-outline-primary w-100">Google konta</a>
            </div>

            <!-- TODO: Create a "I forgot password"/"Reset my password" option DONE -->
            <div class="text-center mt-3">
                <a href="reset-password.php" class="text-decoration-none">Zabudnuté heslo?</a>
            </div>

            <div class="text-center mt-3">
                <p>Nemáte vytvorené konto? <a href="register.php" class="text-decoration-none">Zaregistrujte sa tu.</a></p>
            </div>
        </div>
    </div>
    </main>
    <footer class="bg-dark text-light pt-4 pb-4">
        <div class="text-center mt-4">
            <p class="mb-0">Všetky práva vyhradené &copy; 2025 Juraj Hušek Zadanie na WEBTE2</p>
        </div>
    </footer>
    <script src="js/script-login.js"></script>
</body>
</html>