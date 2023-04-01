<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once('../config.php');

$db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        load_menu($db);
        break;
    case 'POST':
        create_menu($db, $_POST);
        break;
    case 'PUT':
        parse_str(file_get_contents('php://input'), $_PUT);
        update_menu($db, $_GET['id'], $_PUT);
        break;
    case 'DELETE':
        $id = $_GET['id'];
        delete_menu($db, $id);
        break;
}

function load_menu($db) {
}

function create_menu($db, $data) {
}

function update_menu($db, $id, $data) {
}

function delete_menu($db) {
}
