<?php

require_once('config.php');  // $pdo
require_once('Laureate.class.php');

$laureate = new Laureate($db);

header("Content-Type: application/json");

// https://node8.webte.fei.stuba.sk/cv3/api/v0/laureates/1/prizes/1/
// POST, GET, PUT, DELETE - CRUD: Create, Read, Update, Delete

$method = $_SERVER['REQUEST_METHOD'];
$route = explode('/', $_GET['route']);

switch ($method) {
    case 'GET':
        if ($route[0] == 'laureates' && count($route) == 1) {
            http_response_code(200);
            echo json_encode($laureate->index());  // Get all laureates
            break;
        } elseif ($route[0] == 'laureates' && count($route) == 2 && is_numeric($route[1])) {
            $id = $route[1];
            $data = $laureate->show($id);
            if ($data) {
                http_response_code(200);
                echo json_encode($data);
                break;
            }
        }
        elseif ($route[0] == 'laureates' && $route[1] == 'prizes') {
            http_response_code(200);
            echo json_encode($laureate->laureatesWithPrizes());
            break;
        }
        elseif (
            $route[0] == 'laureates' &&
            isset($route[1]) && is_numeric($route[1]) &&
            isset($route[2]) && $route[2] === 'details'
        ) {
            $id = intval($route[1]);
            $details = $laureate->fullDetails($id);
        
            if (!$details || isset($details["error"])) {
                http_response_code(404);
                echo json_encode(["message" => "Laureát neexistuje alebo nastala chyba."]);
            } else {
                http_response_code(200);
                echo json_encode($details);
            }
            break;
        }
        elseif ($route[0] === 'laureates' && isset($route[1]) && $route[1] === 'filters') {
            $filters = $laureate->getAvailableFilters();
        
            if (isset($filters['error'])) {
                http_response_code(500);
                echo json_encode(["message" => "Chyba pri načítaní filtrov", "error" => $filters["error"]]);
            } else {
                http_response_code(200);
                echo json_encode($filters);
            }
            break;
        }        
        elseif ($route[0] === 'laureates' && $route[1] === 'prizes-filtered') {
            $year = $_GET['year'] ?? null;
            $category = $_GET['category'] ?? null;
            $country = $_GET['country'] ?? null;
        
            $filtered = $laureate->filterPrizes($year, $category, $country);
            http_response_code(200);
            echo json_encode($filtered);
            break;
        }
              
        http_response_code(404);
        echo json_encode(['message' => 'Not found']);
        break;
    case 'POST':
        if ($route[0] == 'laureates' && count($route) == 1) {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!isset($data['fullname'], $data['sex'], $data['birth'], $data['prizes']) || !is_array($data['prizes'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Neplatné dáta – chýba meno, pohlavie, dátum narodenia alebo ceny.']);
                break;
            }
    
            $result = $laureate->storeWithPrizes($data);

            if (isset($result['error'])) {
                if ($result['error'] === 'duplicate') {
                    http_response_code(409); 
                } else {
                    http_response_code(500);
                }
                echo json_encode($result);
            } else {
                http_response_code(201);
                echo json_encode($result);
            }
            
    
            break;
        }
        if ($route[0] === 'laureates' && $route[1] === 'batch') {
            $allData = json_decode(file_get_contents("php://input"), true);
            $inserted = [];
            $errors = [];
    
            foreach ($allData as $laureateData) {
                $result = $laureate->storeWithPrizes($laureateData);
                if (isset($result['error'])) {
                    $errors[] = $result;
                } else {
                    $inserted[] = $result;
                }
            }
    
            http_response_code(207); 
            echo json_encode([
                'success' => $inserted,
                'errors' => $errors
            ]);
            break;
        }
        http_response_code(400);
        echo json_encode(['message' => 'Bad request']);
        break;
    case 'PUT':
        if ($route[0] == 'laureates' && count($route) == 2 && is_numeric($route[1])) {
            $currentID = $route[1];
            $currentData = $laureate->show($currentID);
            if (!$currentData) {
                http_response_code(404);
                echo json_encode(['message' => 'Not found']);
                break;
            }

            $updatedData = json_decode(file_get_contents('php://input'), true);
            if (isset($updatedData['birth'])) {
                $updatedData['birth_year'] = $updatedData['birth'];
            }
            if (isset($updatedData['death'])) {
                $updatedData['death_year'] = $updatedData['death'];
            }

            $currentData = array_merge($currentData, $updatedData);

            $status = $laureate->update(
                $currentID,
                $currentData['sex'],
                $currentData['birth_year'],
                $currentData['death_year'],
                $currentData['fullname'],
                $currentData['organisation'],
                $currentData['country'] ?? null  
            );
            

            if ($status != 0) {
                http_response_code(400);
                echo json_encode(['message' => "Bad request", 'data' => $status]);
                break;
            }

            http_response_code(200);
            echo json_encode([
                'message' => "Updated successfully",
            ]);
            break;
        }
        http_response_code(404);
        echo json_encode(['message' => 'Not found']);
        break;
    case 'DELETE':
        if ($route[0] == 'laureates' && count($route) == 2 && is_numeric($route[1])) {
            $id = $route[1];
            $exist = $laureate->show($id);
            if (!$exist) {
                http_response_code(404);
                echo json_encode(['message' => 'Not found']);
                break;
            }

            $status = $laureate->destroy($id);

            if ($status != 0) {
                http_response_code(400);
                echo json_encode(['message' => "Bad request", 'data' => $status]);
                break;
            }

            http_response_code(201);
            echo json_encode(['message' => "Deleted successfully"]);
            break;

        }
        http_response_code(404);
        echo json_encode(['message' => 'Not found']);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}