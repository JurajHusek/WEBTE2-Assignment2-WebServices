<?php

session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}

require_once 'config.php';
require_once 'vendor/autoload.php';

use Google\Client;

$client = new Client();
// Required, call the setAuthConfig function to load authorization credentials from
// client_secret.json file. The file can be downloaded from Google Cloud Console.
$client->setAuthConfig('../../client_secret.json');


// User granted permission as an access token is in the session.
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);

    // Get the user profile info from Google OAuth 2.0.
    $oauth = new Google\Service\Oauth2($client);
    $account_info = $oauth->userinfo->get();

    
    $_SESSION['fullname'] = $account_info->name;
    $_SESSION['gid'] = $account_info->id;
    $_SESSION['email'] = $account_info->email;

}
if (!isset($_SESSION['user_id']) && isset($_SESSION['email'])) {
    // Skontrolovať, či užívateľ existuje v DB
    $stmt = $db->prepare("SELECT id, created_at FROM users WHERE email = :email");
    $stmt->bindParam(":email", $_SESSION['email'], PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id'] = $user["id"]; // Uloženie ID používateľa do session
        $_SESSION['created_at'] = $user["created_at"]; // Dátum vytvorenia účtu
    }
}

$user_id = $_SESSION["user_id"] ?? null; // Ak nie je user_id, bude null
$gid = $_SESSION["gid"] ?? null; // Ak nie je gid, bude null


if ($user_id) {
    $stmt = $db->prepare("SELECT login_time, login_type FROM users_login WHERE user_id = :id ORDER BY login_time DESC");
    $stmt->bindParam(":id", $user_id, PDO::PARAM_INT);
} elseif (isset($_SESSION['email'])) {
    $stmt = $db->prepare("SELECT login_time, login_type FROM users_login WHERE email = :email ORDER BY login_time DESC");
    $stmt->bindParam(":email", $_SESSION['email'], PDO::PARAM_STR);
} else {
    die("Chyba: Neznámy používateľ.");
}


$stmt->execute();
$login_history = $stmt->fetchAll(PDO::FETCH_ASSOC);


// TODO: Provide the user with the option to temporarily disable or reset 2FA. not done :(
// TODO: Provide the user with the option to reset the password. DONE
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_profile"])) {
        $firstname = trim($_POST["firstname"]);
        $lastname = trim($_POST["lastname"]);

        if (empty($firstname) || empty($lastname)) {
            $_SESSION["update_message"] = "Meno a priezvisko nesmú byť prázdne.";
            $_SESSION["update_success"] = false;
        } elseif (strlen($firstname) + strlen($lastname) > 128) {
            $_SESSION["update_message"] = "Meno a priezvisko dokopy nesmú presiahnuť 128 znakov.";
            $_SESSION["update_success"] = false;
        } else {
            // Regex pre povolené znaky (len písmená a diakritika)
            $namePattern = "/^[\p{L}\s-]+$/u";

            if (!preg_match($namePattern, $firstname) || !preg_match($namePattern, $lastname)) {
                $_SESSION["update_message"] = "Meno a priezvisko môžu obsahovať iba písmená a medzery.";
                $_SESSION["update_success"] = false;
            } else {
                // update v db
                $stmt = $db->prepare("UPDATE users SET fullname = :fullname WHERE id = :id");
                $new_fullname = $firstname . " " . $lastname;
                $stmt->bindParam(":fullname", $new_fullname, PDO::PARAM_STR);
                $stmt->bindParam(":id", $_SESSION["user_id"], PDO::PARAM_INT);

                if ($stmt->execute()) {
                    $_SESSION["fullname"] = $new_fullname;
                    $_SESSION["update_message"] = "Osobné údaje boli úspešne aktualizované!";
                    $_SESSION["update_success"] = true;
                } else {
                    $_SESSION["update_message"] = "Chyba pri aktualizácii údajov.";
                    $_SESSION["update_success"] = false;
                }
            }
        }

        header("Location: restricted.php");
        exit;
    }

?>
<!doctype html>
<html lang="sk">

<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>WEBTE2 Nobels - Dashboard</title>
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
        <div class="container d-flex justify-content-center align-items-top vh-50">
            <div class="card p-4 shadow w-75 text-center" style="width=80%">
                <h1 class="mb-3">Dashboard</h1>
                <h3 class="mb-3">Dobrý deň, <?= htmlspecialchars($_SESSION['fullname']); ?></h3>
                <p><strong>e-mail:</strong> <?= htmlspecialchars($_SESSION['email']); ?></p>
                <?php if (isset($_SESSION['gid'])) : ?>
                    <p><strong>Si prihlásený cez Google účet, ID:</strong> <?= htmlspecialchars($_SESSION['gid']); ?></p>
                    
                    <?php if (!empty($_SESSION['user_id'])) : ?>
                        <p><strong>Dátum vytvorenia konta:</strong> <?= htmlspecialchars($_SESSION['created_at']); ?></p>
                        <div class="mt-4">
                            <a href="logout.php" class="btn btn-danger me-2">Odhlásenie</a>
                            <button type="button" class="btn btn-secondary" id="changeDetailsButton">
                                Zmena osobných údajov
                            </button>
                            <a href="update-password.php" class="btn btn-secondary">Zmena hesla</a>
                            <a href="admin-panel.php" class="btn btn-secondary">Admin panel</a>
                        </div>
                    <?php else : ?>
                        <div class="mt-4">
                            <a href="logout.php" class="btn btn-danger">Odhlásenie</a>
                        </div>
                    <?php endif; ?>

                <?php else : ?>
                    <p><strong>Si prihlásený cez lokálne údaje.</strong></p>
                    <p><strong>Dátum vytvorenia konta:</strong> <?= htmlspecialchars($_SESSION['created_at']); ?></p>
                    <div class="mt-4">
                        <a href="logout.php" class="btn btn-danger me-2">Odhlásenie</a>
                        <button type="button" class="btn btn-secondary" id="changeDetailsButton">
                            Zmena osobných údajov
                        </button>
                        <a href="update-password.php" class="btn btn-secondary">Zmena hesla</a>
                        <a href="admin-panel.php" class="btn btn-secondary">Admin panel</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="container mt-5">
            <h3 class="text-center">História prihlásení</h3>
            <table id="loginHistoryTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Čas prihlásenia</th>
                        <th>Typ prihlásenia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($login_history as $login) : ?>
                        <tr>
                            <td><?= htmlspecialchars($login["login_time"]); ?></td>
                            <td><?= htmlspecialchars($login["login_type"]); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="container mt-5" id="changeDetails" hidden>
            <div class="card p-4 shadow">
                <h3 class="text-center mb-3">Zmena osobných údajov</h3>
                <?php if (isset($_SESSION['update_message'])): ?>
                    <div class="alert <?= $_SESSION['update_success'] ? 'alert-success' : 'alert-danger' ?>" role="alert">
                        <?= htmlspecialchars($_SESSION['update_message']) ?>
                    </div>
                    <?php unset($_SESSION['update_message'], $_SESSION['update_success']); ?>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3"> 
                        <label class="form-label">Aktuálne meno a priezvisko</label>
                        <input class="form-control" value="<?= htmlspecialchars($_SESSION['fullname']) ?>" disabled>
                    </div>
                    <div class="mb-3"> 
                        <label for="firstname" class="form-label">Nové meno</label>
                        <input type="text" class="form-control" id="firstname" name="firstname" required>
                        <p class="error" id="firstnameError"></p>
                    </div>
                    <div class="mb-3">
                        <label for="lastname" class="form-label">Nové priezvisko</label>
                        <input type="text" class="form-control" id="lastname" name="lastname" required>
                        <p class="error" id="lastnameError"></p>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary w-100">Uložiť zmeny</button>
                </form>
            </div>
        </div>

    </main>
    <footer class="bg-dark text-light pt-4 pb-4">
        <div class="text-center mt-4">
            <p class="mb-0">Všetky práva vyhradené &copy; 2025 Juraj Hušek Zadanie na WEBTE2</p>
        </div>
    </footer>
    <script src="js/script-restricted.js"></script>
</body>

</html>