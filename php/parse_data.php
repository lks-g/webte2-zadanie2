<?php

require_once('../config.php');

try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname;charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT * FROM menus WHERE provider_id = 2 AND source_code IS NOT NULL ORDER BY download_time DESC LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute();

    $menu = $stmt->fetch(PDO::FETCH_ASSOC);
    $doc = new DOMDocument();
    @$doc->loadHTML($menu['source_code']);
    $xpath = new DOMXPath($doc);

    $menu_type_element = $xpath->query('//h5[@class="mb-4 cursive-title primary"]');
    $menu_type = $menu_type_element[0]->nodeValue;

    $dishes = $xpath->query('//div[@class="leftbar"]/h5');

    $prices = $xpath->query('//div[@class="rightbar"]/h5');

    $location_element = $xpath->query('//div[@class="details"]/p[1]');
    $location_parts = explode("\n", trim($location_element[0]->nodeValue));
    $location = trim(end($location_parts));

    foreach ($dishes as $index => $dish) {
        $name = $db->quote($dish->nodeValue);
        $price_str = $prices[$index]->nodeValue;
        $price_parts = explode('/', $price_str);
        $price = $db->quote(trim($price_parts[0]));
        $sql = "INSERT INTO dishes (menu_type, name, price, location) VALUES ('$menu_type', $name, $price, '$location')";
        $db->exec($sql);
    }

} catch (PDOException $e) {
    echo $e->getMessage();
}

?>