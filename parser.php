<?php
require_once("config.php"); 
require_once("index.php");  

// Simple CSV file parser
function parseCSV($filename) {
    $handle = fopen($filename, "r");
    $data = array();
    while (($row = fgetcsv($handle, 0, ";")) !== FALSE) {
        $data[] = array_filter($row);  // push only non-empty values
    }
    fclose($handle);
    
    unset($data[0]);  // remove the first row (column names)
    return $data;
}

// Funkcia na kontrolu existencie záznamu
function checkIfExists($db, $table, $column, $value) {
    $stmt = $db->prepare("SELECT id FROM $table WHERE $column = :value");
    $stmt->bindParam(':value', $value, PDO::PARAM_STR);
    $stmt->execute();
    
    return $stmt->fetchColumn();  // Vráti ID, ak existuje, inak null
}



function importDataFromCSVfyz($db) {

    echo "Importujem fyzika.csv...<br>";
    $data = parseCSV("fyzika.csv");

    if (empty($data)) {
        echo "Súbor fyzika.csv je prázdny!<br>";
        return;
    }
    $category = "fyzika";
    foreach ($data as $row) {
        if (count($row) < 9) continue; 

        $year = $row[0];
        $name = $row[1];
        $surname = $row[2];
        $sex = $row[3];
        $birth_year = $row[4];
        $death_year = !empty($row[5]) ? $row[5] : NULL;
        $country = $row[6];
        $contrib_sk = $row[7];  
        $contrib_en = $row[8];  

        try {
            $country_id = checkIfExists($db, "countries", "country_name", $country);
            if (!$country_id) {
                insertCountry($db, $country);
                $country_id = $db->lastInsertId();
            }

            $fullname = "$name $surname";
            $laureate_id = checkIfExists($db, "laureates", "fullname", $fullname);
            if (!$laureate_id) {
                insertLaureate($db, $name, $surname, NULL, $sex, $birth_year, $death_year);
                $laureate_id = $db->lastInsertId();
            }

            boundCountry($db, $laureate_id, $country_id);

            $prize_id = checkIfExists($db, "prizes", "contrib_sk", $contrib_sk);
            if (!$prize_id) {
                insertPrize($db, $year, $category, $contrib_sk, $contrib_en,NULL);
                $prize_id = $db->lastInsertId();
            }

            boundLaureatePrize($db, $laureate_id, $prize_id);
    

            echo "Pridaný záznam pre: $name $surname ($year).<br>";

        } catch (Exception $e) {
            echo "Chyba pri importe $name $surname: " . $e->getMessage() . "<br>";
        }
    }
}
function importDataFromCSVchem($db) {

    echo "Importujem chemia.csv...<br>";

    $data = parseCSV("chemia.csv");

    if (empty($data)) {
        echo "Súbor chemia.csv je prázdny!<br>";
        return;
    }
    $category = "chémia";
    foreach ($data as $row) {
        if (count($row) < 9) continue; 

        $year = $row[0];
        $name = $row[1];
        $surname = $row[2];
        $sex = $row[3];
        $birth_year = $row[4];
        $death_year = !empty($row[5]) ? $row[5] : NULL;
        $country = $row[6];
        $contrib_sk = $row[7];  
        $contrib_en = $row[8];  

        try {
            $country_id = checkIfExists($db, "countries", "country_name", $country);
            if (!$country_id) {
                insertCountry($db, $country);
                $country_id = $db->lastInsertId();
            }

            $fullname = "$name $surname";
            $laureate_id = checkIfExists($db, "laureates", "fullname", $fullname);
            if (!$laureate_id) {
                insertLaureate($db, $name, $surname, NULL, $sex, $birth_year, $death_year);
                $laureate_id = $db->lastInsertId();
            }

            boundCountry($db, $laureate_id, $country_id);

           
            $prize_id = checkIfExists($db, "prizes", "contrib_sk", $contrib_sk);
            if (!$prize_id) {
                insertPrize($db, $year, $category, $contrib_sk, $contrib_en,NULL);
                $prize_id = $db->lastInsertId();
            }
            
            boundLaureatePrize($db, $laureate_id, $prize_id);
    

            echo "Pridaný záznam pre: $name $surname ($year).<br>";

        } catch (Exception $e) {
            echo "Chyba pri importe $name $surname: " . $e->getMessage() . "<br>";
        }
    }
}
function importDataFromCSVmed($db) {

    echo "Importujem medicina.csv...<br>";

    $data = parseCSV("medicina.csv");

    if (empty($data)) {
        echo "Súbor medicina.csv je prázdny!<br>";
        return;
    }

    $category = "medicína";
    foreach ($data as $row) {
        if (count($row) < 9) continue; 

        $year = $row[0];
        $name = $row[1];
        $surname = $row[2];
        $sex = $row[3];
        $birth_year = $row[4];
        $death_year = !empty($row[5]) ? $row[5] : NULL;
        $country = $row[6];
        $contrib_sk = $row[7]; 
        $contrib_en = $row[8];  

        try {
            $country_id = checkIfExists($db, "countries", "country_name", $country);
            if (!$country_id) {
                insertCountry($db, $country);
                $country_id = $db->lastInsertId();
            }

            $fullname = "$name $surname";
            $laureate_id = checkIfExists($db, "laureates", "fullname", $fullname);
            if (!$laureate_id) {
                insertLaureate($db, $name, $surname, NULL, $sex, $birth_year, $death_year);
                $laureate_id = $db->lastInsertId();
            }

            boundCountry($db, $laureate_id, $country_id);

            $prize_id = checkIfExists($db, "prizes", "contrib_sk", $contrib_sk);
            if (!$prize_id) {
                insertPrize($db, $year, $category, $contrib_sk, $contrib_en,NULL);
                $prize_id = $db->lastInsertId();
            }

            boundLaureatePrize($db, $laureate_id, $prize_id);
    

            echo "Pridaný záznam pre: $name $surname ($year).<br>";

        } catch (Exception $e) {
            echo "Chyba pri importe $name $surname: " . $e->getMessage() . "<br>";
        }
    }
}
function importDataFromCSVlit($db) {

    echo "Importujem literatura.csv...<br>";
    $data = parseCSV("literatura.csv");

    if (empty($data)) {
        echo "Súbor literatura.csv je prázdny!<br>";
        return;
    }

    $category = "literatúra";
    foreach ($data as $row) {
        if (count($row) < 13) continue; 

        $year = $row[0];
        $name = $row[1];
        $surname = $row[2];
        $sex = $row[3];
        $birth_year = $row[4];
        $death_year = !empty($row[5]) ? $row[5] : NULL;
        $country = $row[6];
        $contrib_sk = $row[7];  
        $contrib_en = $row[8];  
        $language_sk = $row[9];
        $language_en = $row[10];
        $genre_sk = $row[11];
        $genre_en = $row[12];
        try {
            $country_id = checkIfExists($db, "countries", "country_name", $country);
            if (!$country_id) {
                insertCountry($db, $country);
                $country_id = $db->lastInsertId();
            }

            $fullname = "$name $surname";
            $laureate_id = checkIfExists($db, "laureates", "fullname", $fullname);
            if (!$laureate_id) {
                insertLaureate($db, $name, $surname, NULL, $sex, $birth_year, $death_year);
                $laureate_id = $db->lastInsertId();
            }

            boundCountry($db, $laureate_id, $country_id);

            insertPrizeDetails($db, $language_sk, $language_en, $genre_sk, $genre_en);
            $prize_details_id = $db->lastInsertId();
            $prize_id = checkIfExists($db, "prizes", "contrib_sk", $contrib_sk);
            if (!$prize_id) {
                insertPrize($db, $year, $category, $contrib_sk, $contrib_en, $prize_details_id);
                $prize_id = $db->lastInsertId();
            }

            boundLaureatePrize($db, $laureate_id, $prize_id);
            

            echo "Pridaný záznam pre: $name $surname ($year).<br>";

        } catch (Exception $e) {
            echo "Chyba pri importe $name $surname: " . $e->getMessage() . "<br>";
        }
    }
}
function importDataFromCSVmier($db) {

    echo "Importujem mier.csv...<br>";

    $data = parseCSV("mier.csv");

    if (empty($data)) {
        echo "Súbor mier.csv je prázdny!<br>";
        return;
    }

    $category = "mier";
    foreach ($data as $row) {
        
        $year = $row[0];
        $name = !empty($row[1]) ? $row[1] : NULL;
        $surname = !empty($row[2]) ? $row[2] : NULL;
        $organisation = !empty($row[3]) ? $row[3] : NULL;
        $sex = !empty($row[4]) ? $row[4] : NULL;
        $birth_year = !empty($row[5]) ? $row[5] : NULL;;
        $death_year = !empty($row[6]) ? $row[6] : NULL;
        $country = $row[7];
        $contrib_sk = $row[8];  
        $contrib_en = $row[9];  
        try {
            $country_id = checkIfExists($db, "countries", "country_name", $country);
            if (!$country_id) {
                insertCountry($db, $country);
                $country_id = $db->lastInsertId();
            }

            if($organisation != NULL) {
                $laureate_id = checkIfExists($db,"laureates","organisation",$organisation);
                if (!$laureate_id) {
                    insertLaureate($db, NULL, NULL, $organisation, NULL, $birth_year, NULL);
                    $laureate_id = $db->lastInsertId();
                }
            } else {
                $fullname = "$name $surname";
                $laureate_id = checkIfExists($db, "laureates", "fullname", $fullname);
                if (!$laureate_id) {
                    insertLaureate($db, $name, $surname, NULL, $sex, $birth_year, $death_year);
                    $laureate_id = $db->lastInsertId();
                }
            } 
            boundCountry($db, $laureate_id, $country_id);

            $prize_id = checkIfExists($db, "prizes", "contrib_sk", $contrib_sk);
            if (!$prize_id) {
                insertPrize($db, $year, $category, $contrib_sk, $contrib_en, NULL);
                $prize_id = $db->lastInsertId();
            }

            boundLaureatePrize($db, $laureate_id, $prize_id);
            echo "Pridaný záznam pre: $name $surname ($year).<br>";
        } 
        catch (Exception $e) {
            echo "Chyba pri importe $name $surname: " . $e->getMessage() . "<br>";
        }
    }
}

//importDataFromCSVfyz($db);
//importDataFromCSVchem($db);
//importDataFromCSVmed($db);
//importDataFromCSVlit($db);
//importDataFromCSVmier($db);
//echo "<br>**Import dokončený!";
echo "Import nie je povolený.";