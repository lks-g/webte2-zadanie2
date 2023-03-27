<?php

require_once('../config.php');
header('Content-Type: application/json');

try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $menu_date = $_GET['menu_date'];
    if (!strtotime($menu_date)) {
        http_response_code(400);
        exit('[ERROR] - Invalid date format');
    }
    $menu_date = date('Y-m-d', strtotime($menu_date));
    $menu = fetchMenuData($menu_date);
    echo json_encode($menu);

} catch (PDOException $e) {
    echo $e->getMessage();
    exit();
}

function fetchMenuData($menu_date) {
    global $db;
    
    $sql = "SELECT dishes.name, dishes.price, dishes.location, dishes.image_url, providers.name AS provider_name
            FROM dishes
            JOIN menus ON dishes.menu_id = menus.menu_id
            JOIN providers ON menus.provider_id = providers.provider_id
            WHERE menus.menu_date = :menu_date";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':menu_date', $menu_date, PDO::PARAM_STR);
    $stmt->execute();

    $menu = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $provider_name = $row['provider_name'];
        $name = $row['name'];
        $price = $row['price'];
        $location = $row['location'];
        $image_url = $row['image_url'];
        
        $menu[$provider_name][] = array(
            'name' => $name,
            'price' => $price,
            'location' => $location,
            'image_url' => $image_url
        );
    }
    return $menu;
}
?>