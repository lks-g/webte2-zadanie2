<?php

require_once('../config.php');

try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Select the latest downloaded menus with non-null source codes
    $sql = "SELECT * FROM menus WHERE source_code IS NOT NULL ORDER BY download_time DESC LIMIT 3";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($menus as $menu) {
        $doc = new DOMDocument();
        @$doc->loadHTML($menu['source_code']);
    
        // Get the menu_id
        $menu_id_element = $doc->getElementsByTagName('h5')->item(0);
        $menu_id = $menu_id_element->textContent;
    
        // Get the menu items
        $menu_items = $doc->getElementsByTagName('li');
        foreach ($menu_items as $item) {
            // Get the name and price of the item
            $name_element = $item->getElementsByTagName('h5')->item(0);
            $name = $name_element->textContent;
    
            $price_element = $item->getElementsByTagName('h5')->item(1);
            $price = $price_element->textContent;
    
            // Get the location
            $location_element = $doc->getElementById('location');
            $location = $location_element->textContent;
    
            // Insert the menu item into the database
            $stmt = $db->prepare("INSERT INTO menu_items (menu_id, name, price, location) VALUES (:menu_id, :name, :price, :location)");
            $stmt->bindParam(':menu_id', $menu_id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':location', $location);
            $stmt->execute();
        }
    }

} catch (PDOException $e) {
    echo $e->getMessage();
}
