<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once('../config.php');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($_SERVER['REQUEST_URI'] == '/menus') {
        // Endpoint for retrieving all menus
        // Example usage: GET /menus
        // Returns a JSON array of all menus and their details
        // Format: [{"menu_id": 1, "provider_id": 1, "menu_date": "2023-04-05", "source_code": "Sample source code", "download_date": "2023-04-05 08:00:00"}]
        $stmt = $pdo->prepare("SELECT * FROM menus");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($results);
    } else if (preg_match('/\/menus\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
        // Endpoint for retrieving a specific menu by ID
        // Example usage: GET /menus/1
        // Returns a JSON object of the menu details
        // Format: {"menu_id": 1, "provider_id": 1, "menu_date": "2023-04-05", "source_code": "Sample source code", "download_date": "2023-04-05 08:00:00"}
        $stmt = $pdo->prepare("SELECT * FROM menus WHERE menu_id = ?");
        $stmt->execute([$matches[1]]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Menu not found"]);
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_SERVER['REQUEST_URI'] == '/menus') {
        // Endpoint for adding a new menu
        // Example usage: POST /menus (with JSON data in the request body)
        // Required data: {"provider_id": 1, "menu_date": "2023-04-05", "source_code": "Sample source code", "download_date": "2023-04-05 08:00:00"}
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare("INSERT INTO menus (provider_id, menu_date, source_code, download_date) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute([$data['provider_id'], $data['menu_date'], $data['source_code'], $data['download_date']]);
        if ($result) {
            $menu_id = $pdo->lastInsertId();
            http_response_code(201);
            echo json_encode(["menu_id" => $menu_id, "success" => "Menu created"]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Menu creation failed"]);
        }
    } else if (preg_match('/\/menus\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
            // Endpoint for deleting a specific menu by ID
            // Example usage: DELETE /menus/1
            $stmt = $pdo->prepare("DELETE FROM menus WHERE menu_id = ?");
            $result = $stmt->execute([$matches[1]]);
            if ($result) {
                http_response_code(204);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Menu not found"]);
            }
        } else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            // Endpoint for updating a specific menu by ID
            // Example usage: PUT /menus/1 (with JSON data in the request body)
            // Required data: {"provider_id": 1, "menu_date": "2023-04-05", "source_code": "Sample source code", "download_date": "2023-04-05 08:00:00"}
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare("UPDATE menus SET provider_id = ?, menu_date = ?, source_code = ?, download_date = ? WHERE menu_id = ?");
            $result = $stmt->execute([$data['provider_id'], $data['menu_date'], $data['source_code'], $data['download_date'], $matches[1]]);
            if ($result) {
                http_response_code(200);
                echo json_encode(["success" => "Menu updated"]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Menu not found"]);
            }
        }
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
}
