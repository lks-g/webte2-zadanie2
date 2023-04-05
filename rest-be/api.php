<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once('../config.php');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$request_uri = $_SERVER['REQUEST_URI'];
$uri_parts = parse_url($request_uri);
$path = $uri_parts['path'];
$segments = explode('/', $path);

$resource_names = array('menus');

if (!in_array($segments[1], $resource_names)) {
    http_response_code(404);
    echo json_encode(['error' => 'Resource not found']);
    exit;
}

$id = null;
if (isset($segments[2])) {
    $id = intval($segments[2]);
}

$allowed_methods = array(
    'menus' => array(
        'GET' => array(
            'function' => 'get_menus',
            'description' => 'Retrieve all menus or a specific menu by ID'
        ),
        'POST' => array(
            'function' => 'create_menu',
            'description' => 'Create a new menu'
        ),
        'PUT' => array(
            'function' => 'update_menu',
            'description' => 'Update an existing menu by ID'
        ),
        'DELETE' => array(
            'function' => 'delete_menu',
            'description' => 'Delete an existing menu by ID'
        )
    )
);

$method = $_SERVER['REQUEST_METHOD'];
if (!isset($allowed_methods[$segments[1]][$method])) {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$function_name = $allowed_methods[$segments[1]][$method]['function'];
$function_description = $allowed_methods[$segments[1]][$method]['description'];
$response = call_user_func($function_name, $pdo, $id, $method);

echo json_encode($response);

/**
 * GET menu by ID from database
 * @param $pdo
 * @param $id
 * @return void
 */
function get_menus($pdo, $id) {
    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM menus WHERE menu_id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            http_response_code(404);
            return ['error' => 'Menu not found'];
        }
        return $result;
    } else {
        $stmt = $pdo->prepare("SELECT * FROM menus");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }
}

/**
 * CREATE new menu
 * @param $pdo
 * @return void
 */
function create_menu($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("INSERT INTO menus (provider_id, menu_date, source_code, download_date) VALUES (?, ?, ?, ?)");
    $result = $stmt->execute([$data['provider_id'], $data['menu_date'], $data['source_code'], $data['download_date']]);
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("INSERT INTO menus (provider_id, menu_date, source_code, download_date) VALUES (?, ?, ?, ?)");
    $result = $stmt->execute([$data['provider_id'], $data['menu_date'], $data['source_code'], $data['download_date']]);
    if (!$result) {
        http_response_code(500);
        return ['error' => 'Failed to create menu'];
    }
    $menu_id = $pdo->lastInsertId();
    $stmt = $pdo->prepare("SELECT * FROM menus WHERE menu_id = ?");
    $stmt->execute([$menu_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}

/**
 * UPDATE specified menu using ID
 * @param $pdo
 * @param $id
 * @return void
 */
function update_menu($pdo, $id) {
    if (!$id) {
        http_response_code(400);
        return ['error' => 'Missing menu ID'];
    }
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("UPDATE menus SET provider_id = ?, menu_date = ?, source_code = ?, download_date = ? WHERE menu_id = ?");
    $result = $stmt->execute([$data['provider_id'], $data['menu_date'], $data['source_code'], $data['download_date'], $id]);
    if (!$result) {
        http_response_code(500);
        return ['error' => 'Failed to update menu'];
    }
    $stmt = $pdo->prepare("SELECT * FROM menus WHERE menu_id = ?");
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}

/**
 * DELETE menu by ID
 * @param $pdo
 * @param $id
 * @return void
 */
function delete_menu($pdo, $id) {
    if (!$id) {
        http_response_code(400);
        return ['error' => 'Missing menu ID'];
    }
    $stmt = $pdo->prepare("DELETE FROM menus WHERE menu_id = ?");
    $result = $stmt->execute([$id]);
    if (!$result) {
        http_response_code(500);
        return ['error' => 'Failed to delete menu'];
    }
    return ['success' => true];
}
?>