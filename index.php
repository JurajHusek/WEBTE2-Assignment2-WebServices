<?php

require_once("config.php");

function processStatement($stmt) {
    if ($stmt->execute()) {
        return "Record inserted successfully.";
    } else {
        return "Error inserting record: " . implode(", ", $stmt->errorInfo());
    }
}

function insertLaureate($db, $name, $surname, $organisation, $sex, $birth_year, $death_year) {
    $stmt = $db->prepare("INSERT INTO laureates (fullname, organisation, sex, birth_year, death_year) VALUES (:fullname, :organisation, :sex, :birth_year, :death_year)");
    
    $fullname = $name . " " . $surname;
    
    $stmt->bindParam(':fullname', $fullname, PDO::PARAM_STR);
    $stmt->bindParam(':organisation', $organisation, PDO::PARAM_STR);
    $stmt->bindParam(':sex', $sex, PDO::PARAM_STR);
    $stmt->bindParam(':birth_year', $birth_year, PDO::PARAM_STR);
    $stmt->bindParam(':death_year', $death_year, PDO::PARAM_STR);

    return processStatement($stmt);
}

function insertCountry($db, $country_name) {
    $stmt = $db->prepare("INSERT INTO countries (country_name) VALUES (:country_name)");
    
    $stmt->bindParam(':country_name', $country_name, PDO::PARAM_STR);

    return processStatement($stmt);
}

function boundCountry($db, $laureate_id, $country_id) {
    $stmt = $db->prepare("INSERT INTO laureates_countries (laureate_id, country_id) VALUES (:laureate_id, :country_id)");
    
    $stmt->bindParam(':laureate_id', $laureate_id, PDO::PARAM_INT);
    $stmt->bindParam(':country_id', $country_id, PDO::PARAM_INT);

    return processStatement($stmt);
}

function getLaureatesWithCountry($db) {
    $stmt = $db->prepare("
    SELECT laureates.fullname, laureates.sex, laureates.birth_year, laureates.death_year, countries.country_name 
    FROM laureates 
    LEFT JOIN laureates_countries 
        INNER JOIN countries
        ON laureates_countries.country_id = countries.id
    ON laureates.id = laureates_countries.laureate_id");
    
    $stmt->execute();
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $result;
}

function insertLaureateWithCountry($db, $name, $surname, $organisation, $sex, $birth_year, $death_year, $country_name) {
    $db->beginTransaction();
    
    $status = insertLaureate($db, $name, $surname, $organisation, $sex, $birth_year, $death_year);
    
    if (strpos($status, "Error") !== false) {
        $db->rollBack();
        return $status;
    }
    
    $laureate_id = $db->lastInsertId();
    
    $status = insertCountry($db, $country_name);
    
    if (strpos($status, "Error") !== false) {
        $db->rollBack();
        return $status;
    }
    
    $country_id = $db->lastInsertId();
    
    $status = boundCountry($db, $laureate_id, $country_id);

    if (strpos($status, "Error") !== false) {
        $db->rollBack();
        return $status;
    }

    $db->commit();


    return $status;
}
function insertPrize($db, $year, $category, $contrib_sk, $contrib_en, $details_id) {
    $stmt = $db->prepare("INSERT INTO prizes (year, category, contrib_sk, contrib_en, details_id) 
                          VALUES (:year, :category, :contrib_sk, :contrib_en, :details_id)");
    
    $stmt->bindParam(':year', $year, PDO::PARAM_STR);
    $stmt->bindParam(':category', $category, PDO::PARAM_STR);
    $stmt->bindParam(':contrib_sk', $contrib_sk, PDO::PARAM_STR);
    $stmt->bindParam(':contrib_en', $contrib_en, PDO::PARAM_STR);
    $stmt->bindParam(':details_id', $details_id, PDO::PARAM_INT);

    return processStatement($stmt);
}

function insertPrizeDetails($db, $language_sk, $language_en, $genre_sk, $genre_en) {
    $stmt = $db->prepare("INSERT INTO prize_details (language_sk, language_en, genre_sk, genre_en) 
                          VALUES (:language_sk, :language_en, :genre_sk, :genre_en)");
    
    $stmt->bindParam(':language_sk', $language_sk, PDO::PARAM_STR);
    $stmt->bindParam(':language_en', $language_en, PDO::PARAM_STR);
    $stmt->bindParam(':genre_sk', $genre_sk, PDO::PARAM_STR);
    $stmt->bindParam(':genre_en', $genre_en, PDO::PARAM_STR);

    return processStatement($stmt);
}

function boundLaureatePrize($db, $laureate_id, $prize_id) {
    $stmt = $db->prepare("INSERT INTO laureates_prizes (laureate_id, prize_id) 
                          VALUES (:laureate_id, :prize_id)");
    
    $stmt->bindParam(':laureate_id', $laureate_id, PDO::PARAM_INT);
    $stmt->bindParam(':prize_id', $prize_id, PDO::PARAM_INT);

    return processStatement($stmt);
}

function insertLaureateWithPrize($db, $name, $surname, $organisation, $sex, $birth_year, $death_year, 
                                 $year, $category, $contrib_sk, $contrib_en, $details_id) {
    $db->beginTransaction();
    
    $status = insertLaureate($db, $name, $surname, $organisation, $sex, $birth_year, $death_year);
    
    if (strpos($status, "Error") !== false) {
        $db->rollBack();
        return $status;
    }
    
    $laureate_id = $db->lastInsertId();
    
    $status = insertPrize($db, $year, $category, $contrib_sk, $contrib_en, $details_id);
    
    if (strpos($status, "Error") !== false) {
        $db->rollBack();
        return $status;
    }
    
    $prize_id = $db->lastInsertId();
    
    $status = boundLaureatePrize($db, $laureate_id, $prize_id);

    if (strpos($status, "Error") !== false) {
        $db->rollBack();
        return $status;
    }

    $db->commit();

    return $status;
}

function insertPrizeWithDetails($db, $language_sk, $language_en, $genre_sk, $genre_en, 
                               $year, $category, $contrib_sk, $contrib_en) {
    $db->beginTransaction();
    
    $status = insertPrizeDetails($db, $language_sk, $language_en, $genre_sk, $genre_en);
    
    if (strpos($status, "Error") !== false) {
        $db->rollBack();
        return $status;
    }
    
    $details_id = $db->lastInsertId();
    
    $status = insertPrize($db, $year, $category, $contrib_sk, $contrib_en, $details_id);
    
    if (strpos($status, "Error") !== false) {
        $db->rollBack();
        return $status;
    }

    $db->commit();

    return $status;
}
function getTableData($db) {
    
    $stmt = $db->query("
                SELECT laureates.id,
                CASE 
                    WHEN laureates.fullname IS NOT NULL AND laureates.fullname != '' THEN laureates.fullname
                    WHEN laureates.organisation IS NOT NULL AND laureates.organisation != '' THEN laureates.organisation
                    ELSE 'Neznáme' 
                END AS fullname, 
                prizes.year, 
                prizes.category, 
                COALESCE(countries.country_name, 'Neznáme') AS country_name
                FROM laureates
                JOIN laureates_prizes ON laureates.id = laureates_prizes.laureate_id
                JOIN prizes ON laureates_prizes.prize_id = prizes.id
                LEFT JOIN laureates_countries ON laureates.id = laureates_countries.laureate_id
                LEFT JOIN countries ON laureates_countries.country_id = countries.id
                ORDER BY prizes.year DESC;

            ");

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['fullname']) . "</td>";
        echo "<td>" . htmlspecialchars($row['year']) . "</td>";
        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
        echo "<td>" . htmlspecialchars($row['country_name']) . "</td>";
        echo "<td><a href='person.php?id=" . htmlspecialchars($row['id']) . "' class='btn btn-info btn-sm'>Info</a></td>";
        echo "</tr>";
    }
}
?>

<!doctype html>
<html lang="sk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>WEBTE2 Nobels</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="js/script.js"></script>
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
        <div>
        <?php

        session_start();

        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true ) {
            // User is not logged in, show the link to login and registration form
            echo '<div class="alert alert-primary" role="alert">
                    Pre prístup k administrátorským funkciám sa prosím <a href="login.php" class="alert-link">prihláste alebo registrujte</a>.
                    </div>';
            //echo '<div class="login-info-panel"><p>Pre pokračovanie sa prosím <a href="login.php">prihláste</a> alebo sa <a href="register.php">zaregistrujte</a>.</p></div>';
        } else {
            // User is logged in, show a welcome message.
            echo '<div class="alert alert-light" role="alert">
                    Dobrý deň '. $_SESSION['fullname'] . '
                    </div>';
        }
        ?> 
        <div class="datatable container">
            <h1 class="text-center">Tabuľka nobelistov</h1>
        
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
                    <!-- Dáta sa načítajú dynamicky cez JavaScript z API -->
                </tbody>
            </table>
        </div>
    </main>
    <div id="cookieModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <p>Táto stránka používa cookies.</p>
            </div>
            <div class="modal-footer">
                <button id="acceptCookies" class="btn btn-primary">Rozumiem</button>
            </div>
        </div>
    </div>
</div>
    <footer class="bg-dark text-light pt-4 pb-4">
        <div class="text-center mt-4">
            <p class="mb-0">Všetky práva vyhradené &copy; 2025 Juraj Hušek Zadanie na WEBTE2</p>
        </div>
    </footer>
</body>

</html>