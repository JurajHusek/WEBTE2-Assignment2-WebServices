<?php
/**
 * @OA\Schema(
 *     schema="Laureate",
 *     required={"fullname", "sex", "birth_year"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="fullname", type="string", example="Marie Curie"),
 *     @OA\Property(property="organisation", type="string", example="Sorbonne"),
 *     @OA\Property(property="sex", type="string", example="F"),
 *     @OA\Property(property="birth_year", type="integer", example=1867),
 *     @OA\Property(property="death_year", type="integer", example=1934),
 *     @OA\Property(property="country", type="string", example="France")
 * )
 */
class Laureate {

    private $db;
    // TODO: Implement class field according to the database table.

    public function __construct($db) {
        $this->db = $db;
    }

/**
 * @OA\Get(
 *     path="/laureates",
 *     summary="Získať všetkých laureátov",
 *     description="Vráti úplný zoznam laureátov bez detailov o cenách alebo krajinách.",
 *     operationId="getAllLaureates",
 *     tags={"Laureates"},
 *     @OA\Response(
 *         response=200,
 *         description="Zoznam laureátov",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="fullname", type="string", example="Albert Einstein"),
 *                 @OA\Property(property="organisation", type="string", example="Červený kríž"),
 *                 @OA\Property(property="sex", type="string", example="M"),
 *                 @OA\Property(property="birth_year", type="integer", example=1879),
 *                 @OA\Property(property="death_year", type="integer", example=1955)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Chyba pri načítaní údajov",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Databázová chyba")
 *         )
 *     )
 * )
 */
    public function index() {
        $stmt = $this->db->prepare("SELECT * FROM laureates");
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @OA\Get(
     *     path="/laureates/{id}",
     *     summary="Získať konkrétneho laureáta podľa ID",
     *     tags={"Laureáti"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID laureáta",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detail laureáta",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="fullname", type="string", example="Albert Einstein"),
     *             @OA\Property(property="organisation", type="string", example="Princeton University"),
     *             @OA\Property(property="sex", type="string", enum={"M", "F"}, example="M"),
     *             @OA\Property(property="birth_year", type="integer", example=1879),
     *             @OA\Property(property="death_year", type="integer", example=1955)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Laureát neexistuje",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Not found")
     *         )
     *     )
     * )
     */
    public function show($id) {
        // TODO: Implement Where caluse by fullname, organisation...
        $stmt = $this->db->prepare("SELECT * FROM laureates WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

   
/**
 * @OA\Put(
 *     path="/laureates/{id}",
 *     summary="Aktualizovať údaje o laureátovi",
 *     description="Aktualizuje existujúceho laureáta vrátane jeho základných údajov a prípadne krajiny.",
 *     operationId="updateLaureate",
 *     tags={"Laureates"},
 * 
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID laureáta, ktorý sa má aktualizovať",
 *         @OA\Schema(type="integer", example=123)
 *     ),
 * 
 *     @OA\RequestBody(
 *         required=true,
 *         description="Dáta na aktualizáciu laureáta",
 *         @OA\JsonContent(
 *             required={"sex", "birth_year"},
 *             @OA\Property(property="fullname", type="string", maxLength=255, example="Marie Curie"),
 *             @OA\Property(property="organisation", type="string", maxLength=255, example="Université de Paris"),
 *             @OA\Property(property="sex", type="string", enum={"M", "F"}, example="F"),
 *             @OA\Property(property="birth_year", type="integer", example=1867),
 *             @OA\Property(property="death_year", type="integer", nullable=true, example=1934),
 *             @OA\Property(property="country", type="string", example="Francúzsko")
 *         )
 *     ),
 * 
 *     @OA\Response(
 *         response=200,
 *         description="Úspešná aktualizácia",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Updated successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Chybná požiadavka",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Missing required fields or invalid input")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Laureát neexistuje",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Not found")
 *         )
 *     )
 * )
 */
    public function update($id, $sex, $birth_year, $death_year, $fullname = null, $organisation = null, $country = null) {
        try {
            $this->db->beginTransaction();
    
            $stmt = $this->db->prepare("
                UPDATE laureates 
                SET 
                    fullname = :fullname,
                    organisation = :organisation,
                    sex = :sex,
                    birth_year = :birth_year,
                    death_year = :death_year
                WHERE id = :id
            ");
        
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':fullname', $fullname, PDO::PARAM_STR);
            $stmt->bindParam(':organisation', $organisation, PDO::PARAM_STR);
            $stmt->bindParam(':sex', $sex, PDO::PARAM_STR);
            $stmt->bindParam(':birth_year', $birth_year, PDO::PARAM_STR);
            $stmt->bindParam(':death_year', $death_year, PDO::PARAM_STR);
            $stmt->execute();
            if (!empty($country)) {
                $stmt = $this->db->prepare("SELECT id FROM countries WHERE country_name = :name");
                $stmt->execute([':name' => $country]);
                $countryData = $stmt->fetch(PDO::FETCH_ASSOC);
    
                if ($countryData) {
                    $country_id = $countryData['id'];
                } else {
                    $stmt = $this->db->prepare("INSERT INTO countries (country_name) VALUES (:name)");
                    $stmt->execute([':name' => $country]);
                    $country_id = $this->db->lastInsertId();
                }
                $stmt = $this->db->prepare("SELECT * FROM laureates_countries WHERE laureate_id = :id");
                $stmt->execute([':id' => $id]);
                $exists = $stmt->fetch();
    
                if ($exists) {
                    $stmt = $this->db->prepare("UPDATE laureates_countries SET country_id = :country_id WHERE laureate_id = :id");
                } else {
                    $stmt = $this->db->prepare("INSERT INTO laureates_countries (laureate_id, country_id) VALUES (:id, :country_id)");
                }
                $stmt->execute([':id' => $id, ':country_id' => $country_id]);
            }
    
            $this->db->commit();
            return 0;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return "Error: " . $e->getMessage();
        }
    }
    
    
/**
 * @OA\Delete(
 *     path="/laureates/{id}",
 *     summary="Vymazať laureáta a jeho priradené dáta",
 *     description="Vymaže laureáta, jeho prepojenia na krajiny, ceny a prípadne zmaže aj cenu (a jej detaily), ak už nie je prepojená na iného laureáta.",
 *     operationId="deleteLaureate",
 *     tags={"Laureates"},
 *     
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID laureáta, ktorý sa má vymazať",
 *         @OA\Schema(type="integer", example=123)
 *     ),
 * 
 *     @OA\Response(
 *         response=200,
 *         description="Úspešné vymazanie",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Deleted successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Laureát neexistuje",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Not found")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Chybná požiadavka",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Bad request"),
 *             @OA\Property(property="data", type="string", example="Napr. PDO error message")
 *         )
 *     )
 * )
 */
    public function destroy($id) {
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare("SELECT prize_id FROM laureates_prizes WHERE laureate_id = :id");
            $stmt->execute([':id' => $id]);
            $prizes = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
            $stmt = $this->db->prepare("DELETE FROM laureates_countries WHERE laureate_id = :id");
            $stmt->execute([':id' => $id]);
    
            $stmt = $this->db->prepare("DELETE FROM laureates_prizes WHERE laureate_id = :id");
            $stmt->execute([':id' => $id]);
    
            $stmt = $this->db->prepare("DELETE FROM laureates WHERE id = :id");
            $stmt->execute([':id' => $id]);
    
            foreach ($prizes as $prize_id) {
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM laureates_prizes WHERE prize_id = :prize_id");
                $stmt->execute([':prize_id' => $prize_id]);
                $count = $stmt->fetchColumn();
    
                if ($count == 0) {
                    $stmt = $this->db->prepare("SELECT details_id FROM prizes WHERE id = :id");
                    $stmt->execute([':id' => $prize_id]);
                    $details_id = $stmt->fetchColumn();

                    $stmt = $this->db->prepare("DELETE FROM prizes WHERE id = :id");
                    $stmt->execute([':id' => $prize_id]);
            
                    if ($details_id) {
                        $stmt = $this->db->prepare("DELETE FROM prize_details WHERE id = :id");
                        $stmt->execute([':id' => $details_id]);
                    }
                }
            }
    
            $this->db->commit();
            return 0;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return ["error" => $e->getMessage()];
        }
    }
    
/**
 * @OA\Get(
 *     path="/laureates/prizes",
 *     summary="Získať laureátov s ich Nobelovými cenami",
 *     description="Vráti zoznam všetkých laureátov spolu s ich oceneniami, vrátane roka, kategórie a krajiny.",
 *     operationId="getLaureatesWithPrizes",
 *     tags={"Laureates"},
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Zoznam laureátov s cenami",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=42),
 *                 @OA\Property(property="fullname", type="string", example="Marie Curie"),
 *                 @OA\Property(property="organisation", type="string", example="University of Paris"),
 *                 @OA\Property(property="sex", type="string", example="F"),
 *                 @OA\Property(property="birth_year", type="string", example="1867"),
 *                 @OA\Property(property="death_year", type="string", example="1934"),
 *                 @OA\Property(property="year", type="string", example="1903"),
 *                 @OA\Property(property="category", type="string", example="fyzika"),
 *                 @OA\Property(property="country_name", type="string", example="Francúzsko")
 *             )
 *         )
 *     )
 * )
 */
    public function laureatesWithPrizes() {
        $stmt = $this->db->query("
            SELECT laureates.id,
            CASE 
                WHEN laureates.fullname IS NOT NULL AND laureates.fullname != '' THEN laureates.fullname
                WHEN laureates.organisation IS NOT NULL AND laureates.organisation != '' THEN laureates.organisation
                ELSE 'Neznáme' 
            END AS fullname,
            laureates.organisation,
            laureates.sex,
            laureates.birth_year,
            laureates.death_year,
            prizes.year,
            prizes.category,
            COALESCE(countries.country_name, 'Neznáme') AS country_name
        FROM laureates
        JOIN laureates_prizes ON laureates.id = laureates_prizes.laureate_id
        JOIN prizes ON laureates_prizes.prize_id = prizes.id
        LEFT JOIN laureates_countries ON laureates.id = laureates_countries.laureate_id
        LEFT JOIN countries ON laureates_countries.country_id = countries.id
        ORDER BY prizes.year DESC

        ");
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
/**
 * @OA\Get(
 *     path="/laureates/{id}/details",
 *     summary="Získať kompletné detaily o laureátovi",
 *     description="Vráti detailné informácie o laureátovi vrátane osobných údajov, cien a detailov k literárnym oceneniam.",
 *     operationId="getLaureateFullDetails",
 *     tags={"Laureates"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID laureáta",
 *         @OA\Schema(type="integer", example=123)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Detaily o laureátovi",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="fullname", type="string", example="Gabriel García Márquez"),
 *                 @OA\Property(property="organisation", type="string", example="Červený kríž"),
 *                 @OA\Property(property="sex", type="string", example="M"),
 *                 @OA\Property(property="birth_year", type="string", example="1927"),
 *                 @OA\Property(property="death_year", type="string", example="2014"),
 *                 @OA\Property(property="prize_id", type="integer", example=87),
 *                 @OA\Property(property="prize_year", type="string", example="1982"),
 *                 @OA\Property(property="category", type="string", example="literatúra"),
 *                 @OA\Property(property="contrib_sk", type="string", example="Za jeho romány a poviedky..."),
 *                 @OA\Property(property="contrib_en", type="string", example="For his novels and short stories..."),
 *                 @OA\Property(property="country", type="string", example="Kolumbia"),
 *                 @OA\Property(
 *                     property="details",
 *                     type="object",
 *                     @OA\Property(property="language_sk", type="string", example="španielčina"),
 *                     @OA\Property(property="language_en", type="string", example="Spanish"),
 *                     @OA\Property(property="genre_sk", type="string", example="román"),
 *                     @OA\Property(property="genre_en", type="string", example="novel")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=404, description="Laureát neexistuje alebo chyba")
 * )
 */
    public function fullDetails($id) {
        $stmt = $this->db->prepare("
            SELECT 
                laureates.fullname, 
                laureates.organisation, 
                laureates.sex, 
                laureates.birth_year, 
                laureates.death_year, 
                prizes.id AS prize_id,
                prizes.year AS prize_year, 
                prizes.category, 
                prizes.contrib_sk, 
                prizes.contrib_en, 
                prizes.details_id,
                COALESCE(countries.country_name, 'Neznáme') AS country
            FROM laureates
            JOIN laureates_prizes ON laureates.id = laureates_prizes.laureate_id
            JOIN prizes ON laureates_prizes.prize_id = prizes.id
            LEFT JOIN laureates_countries ON laureates.id = laureates_countries.laureate_id
            LEFT JOIN countries ON laureates_countries.country_id = countries.id
            WHERE laureates.id = :id
        ");
    
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
        try {
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            foreach ($results as &$prize) {
                if (!empty($prize['details_id'])) {
                    $detailsStmt = $this->db->prepare("
                        SELECT language_sk, language_en, genre_sk, genre_en
                        FROM prize_details
                        WHERE id = :id
                    ");
                    $detailsStmt->execute([':id' => $prize['details_id']]);
                    $details = $detailsStmt->fetch(PDO::FETCH_ASSOC);
    
                    if ($details) {
                        $prize['details'] = $details;
                    }
                }
            }
    
            return $results;
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }
    
    
    
/**
 * @OA\Post(
 *     path="/laureates",
 *     summary="Pridať nového laureáta s cenami",
 *     description="Vytvorí nového laureáta vrátane jeho krajiny a pridelených cien. Ak kategória je literatúra, môžu sa zadať aj detaily.",
 *     operationId="createLaureateWithPrizes",
 *     tags={"Laureates"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"fullname", "sex", "birth", "prizes"},
 *             @OA\Property(property="fullname", type="string", example="Albert Einstein"),
 *             @OA\Property(property="organisation", type="string", example="Princeton University"),
 *             @OA\Property(property="sex", type="string", enum={"M", "F"}, example="M"),
 *             @OA\Property(property="birth", type="integer", example=1879),
 *             @OA\Property(property="death", type="integer", nullable=true, example=1955),
 *             @OA\Property(property="country", type="string", example="Švajčiarsko"),
 *             @OA\Property(
 *                 property="prizes",
 *                 type="array",
 *                 @OA\Items(
 *                     required={"year", "category", "contrib_sk", "contrib_en"},
 *                     @OA\Property(property="year", type="integer", example=1921),
 *                     @OA\Property(property="category", type="string", example="fyzika"),
 *                     @OA\Property(property="contrib_sk", type="string", example="Za objav zákona fotoelektrického javu"),
 *                     @OA\Property(property="contrib_en", type="string", example="For the discovery of the photoelectric law"),
 *                     @OA\Property(property="language_sk", type="string", example="nemčina"),
 *                     @OA\Property(property="language_en", type="string", example="German"),
 *                     @OA\Property(property="genre_sk", type="string", example="vedecká literatúra"),
 *                     @OA\Property(property="genre_en", type="string", example="scientific literature")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Laureát bol úspešne pridaný",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Laureát, ceny a krajina úspešne vložené."),
 *             @OA\Property(property="id", type="integer", example=101)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Neplatné dáta alebo duplicita",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="duplicate"),
 *             @OA\Property(property="message", type="string", example="Laureát s daným menom už v databáze existuje.")
 *         )
 *     )
 * )
 */
    public function storeWithPrizes($data) {
        $check = $this->db->prepare("
        SELECT id FROM laureates 
        WHERE fullname = :fullname AND birth_year = :birth AND sex = :sex
        ");
        $check->execute([
        ':fullname' => $data['fullname'],
        ':birth' => $data['birth'],
        ':sex' => $data['sex']
        ]);

        $existing = $check->fetch(PDO::FETCH_ASSOC);
        if ($existing) {
        return [
            "error" => "duplicate",
            "message" => "Laureát s daným menom už v databáze existuje."
        ];
        }

        try {
            $this->db->beginTransaction();
    
            $stmt = $this->db->prepare("
                INSERT INTO laureates (fullname, organisation, sex, birth_year, death_year)
                VALUES (:fullname, :organisation, :sex, :birth, :death)
            ");
            $stmt->execute([
                ':fullname' => $data['fullname'],
                ':organisation' => $data['organisation'],
                ':sex' => $data['sex'], 
                ':birth' => $data['birth'],
                ':death' => $data['death']
            ]);
            $laureate_id = $this->db->lastInsertId();
    
            if (!empty($data['country'])) {
                $country_name = trim($data['country']);
    
                $stmt = $this->db->prepare("SELECT id FROM countries WHERE country_name = :name");
                $stmt->execute([':name' => $country_name]);
                $country = $stmt->fetch(PDO::FETCH_ASSOC);
    
                if ($country) {
                    $country_id = $country['id'];
                } else {
                    $stmt = $this->db->prepare("INSERT INTO countries (country_name) VALUES (:name)");
                    $stmt->execute([':name' => $country_name]);
                    $country_id = $this->db->lastInsertId();
                }
    
                $stmt = $this->db->prepare("
                    INSERT INTO laureates_countries (laureate_id, country_id)
                    VALUES (:laureate_id, :country_id)
                ");
                $stmt->execute([
                    ':laureate_id' => $laureate_id,
                    ':country_id' => $country_id
                ]);
            }
            foreach ($data['prizes'] as $prize) {
                $details_id = null;
    
                if (mb_strtolower($prize['category']) === 'literatúra') {
                    $stmt = $this->db->prepare("
                        INSERT INTO prize_details (language_sk, language_en, genre_sk, genre_en)
                        VALUES (:language_sk, :language_en, :genre_sk, :genre_en)
                    ");
                    $stmt->execute([
                        ':language_sk' => $prize['language_sk'] ?? '',
                        ':language_en' => $prize['language_en'] ?? '',
                        ':genre_sk' => $prize['genre_sk'] ?? '',
                        ':genre_en' => $prize['genre_en'] ?? ''
                    ]);
                    $details_id = $this->db->lastInsertId();
                }
    
                $stmt = $this->db->prepare("
                    INSERT INTO prizes (year, category, contrib_sk, contrib_en, details_id)
                    VALUES (:year, :category, :contrib_sk, :contrib_en, :details_id)
                ");
                $stmt->execute([
                    ':year' => $prize['year'],
                    ':category' => $prize['category'],
                    ':contrib_sk' => $prize['contrib_sk'],
                    ':contrib_en' => $prize['contrib_en'],
                    ':details_id' => $details_id
                ]);
                $prize_id = $this->db->lastInsertId();
    
                $stmt = $this->db->prepare("
                    INSERT INTO laureates_prizes (laureate_id, prize_id)
                    VALUES (:laureate_id, :prize_id)
                ");
                $stmt->execute([
                    ':laureate_id' => $laureate_id,
                    ':prize_id' => $prize_id
                ]);
            }
    
            $this->db->commit();
            return ["message" => "Laureát, ceny a krajina úspešne vložené.", "id" => $laureate_id];
    
        } catch (PDOException $e) {
            $this->db->rollBack();
            return ["error" => $e->getMessage()];
        }
    }
/**
 * @OA\Get(
 *     path="/laureates/filters",
 *     summary="Získať dostupné filtre (rok, kategória)",
 *     description="Vráti zoznam všetkých dostupných rokov a kategórií, ktoré sa používajú pri filtrovaní laureátov.",
 *     tags={"Laureates"},
 *     operationId="getAvailableFilters",
 *     @OA\Response(
 *         response=200,
 *         description="Roky a kategórie pre filtrovanie",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="years",
 *                 type="array",
 *                 @OA\Items(type="integer", example=2023)
 *             ),
 *             @OA\Property(
 *                 property="categories",
 *                 type="array",
 *                 @OA\Items(type="string", example="fyzika")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Chyba pri získavaní filtrov",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Chyba pri načítaní filtrov"),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[42000]: ...")
 *         )
 *     )
 * )
 */
    public function getAvailableFilters() {
        try {
            $yearsStmt = $this->db->query("SELECT DISTINCT year FROM prizes ORDER BY year DESC");
            $years = $yearsStmt->fetchAll(PDO::FETCH_COLUMN);
    
            $categoriesStmt = $this->db->query("SELECT DISTINCT category FROM prizes ORDER BY category ASC");
            $categories = $categoriesStmt->fetchAll(PDO::FETCH_COLUMN);
    
            return [
                "years" => $years,
                "categories" => $categories
            ];
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }
/**
 * @OA\Get(
 *     path="/laureates/prizes-filtered",
 *     summary="Filtrovať laureátov podľa roku, kategórie a krajiny",
 *     description="Vráti zoznam laureátov podľa zvoleného filtra: rok, kategória a krajina. Nezadané parametre nebudú použité vo filtrovaní.",
 *     operationId="filterPrizes",
 *     tags={"Laureates"},
 *     @OA\Parameter(
 *         name="year",
 *         in="query",
 *         required=false,
 *         description="Rok udelenia ceny",
 *         @OA\Schema(type="integer", example=2022)
 *     ),
 *     @OA\Parameter(
 *         name="category",
 *         in="query",
 *         required=false,
 *         description="Kategória Nobelovej ceny",
 *         @OA\Schema(type="string", example="chémia")
 *     ),
 *     @OA\Parameter(
 *         name="country",
 *         in="query",
 *         required=false,
 *         description="Krajina laureáta",
 *         @OA\Schema(type="string", example="Švédsko")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Zoznam filtrovaných laureátov",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=5),
 *                 @OA\Property(property="fullname", type="string", example="Marie Curie"),
 *                 @OA\Property(property="organisation", type="string", example="Univerzita Sorbonne"),
 *                 @OA\Property(property="year", type="integer", example=1911),
 *                 @OA\Property(property="category", type="string", example="chémia"),
 *                 @OA\Property(property="country_name", type="string", example="Francúzsko")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Chyba pri filtrovaní",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="SQL error")
 *         )
 *     )
 * )
 */
    public function filterPrizes($year = null, $category = null, $country = null) {
        $query = "
        SELECT 
            laureates.id, 
            laureates.fullname, 
            laureates.organisation,
            prizes.year, 
            prizes.category, 
            COALESCE(countries.country_name, 'Neznáme') AS country_name
        FROM laureates
        JOIN laureates_prizes ON laureates.id = laureates_prizes.laureate_id
        JOIN prizes ON laureates_prizes.prize_id = prizes.id
        LEFT JOIN laureates_countries ON laureates.id = laureates_countries.laureate_id
        LEFT JOIN countries ON laureates_countries.country_id = countries.id
        WHERE 1=1
    ";
    
    
        $params = [];
    
        if ($year !== null) {
            $query .= " AND prizes.year = :year";
            $params[':year'] = $year;
        }
    
        if ($category !== null) {
            $query .= " AND prizes.category = :category";
            $params[':category'] = $category;
        }
    
        if ($country !== null) {
            $query .= " AND countries.country_name = :country";
            $params[':country'] = $country;
        }
    
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}