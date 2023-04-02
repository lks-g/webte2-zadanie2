<?php


require_once('../config.php');

try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Select the latest downloaded menus with non-null source codes
    $sql = "SELECT * FROM menus WHERE source_code IS NOT NULL ORDER BY download_time DESC LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $menu = $stmt->fetch(PDO::FETCH_ASSOC);

    $doc = new DOMDocument();
    @$doc->loadHTML($menu['source_code']);

    // Get the menu date
    $menu_date_element = $doc->getElementsByTagName('a')->item(5);
    $menu_date_str = $menu_date_element->textContent;
    $menu_date = date('Y-m-d', strtotime(str_replace('/', '-', $menu_date_str)));

    // Check if menu already exists
    $sql = "SELECT * FROM menus WHERE provider_id = :provider_id AND menu_date = :menu_date";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':provider_id', $menu['provider_id']);
    $stmt->bindParam(':menu_date', $menu_date);
    $stmt->execute();
    $existing_menu = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get the menu items
    $menu_items = $doc->getElementsByTagName('li');
    foreach ($menu_items as $item) {
        // Get the name and price of the item
        $name_element = $item->getElementsByTagName('h5')->item(0);
        $name = $name_element->textContent;

        $price_element = $item->getElementsByTagName('h5')->item(1);
        $price = $price_element->textContent;

        // Extract price from the price string
        $price_regex = "/(\d+\,\d{2})\s\/\s(\d+\,\d{2})/";
        preg_match($price_regex, $price, $matches);
        $price = str_replace(",", ".", $matches[1]);

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

    if (!$existing_menu) {
        // Insert the new menu
        $sql = "INSERT INTO menus (provider_id, menu_date, source_code, download_time) VALUES (:provider_id, :menu_date, :source_code, NOW())";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':provider_id', $menu['provider_id']);
        $stmt->bindParam(':menu_date', $menu_date);
        $stmt->bindParam(':source_code', $menu['source_code']);
        $stmt->execute();
        $menu_id = $db->lastInsertId();

        // Get the menu items
        $menu_items = $doc->getElementsByTagName('div');
        foreach ($menu_items as $item) {
            // Check if it is a menu item
            if ($item->getAttribute('class') === 'menu-body menu-left  menu-white ') {
                // Get the name and price of the item
                $name_element = $item->getElementsByTagName('h4')->item(0);
                $name = $name_element->textContent;

                $price_element = $item->getElementsByTagName('span')->item(0);
                $price = $price_element->textContent;

                // Get the location
                $location = 'Eat&Meet';

                // Get the image URL
                $img_element = $item->getElementsByTagName('img')->item(0);
                $img_url = '';
                if ($img_element) {
                    $img_url = $img_element->getAttribute('src');
                }

                // Insert the menu item into the database
                $sql = "INSERT INTO dishes (menu_id, name, price, location, image_url) VALUES (:menu_id, :name, :price, :location, :img_url)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':menu_id', $menu_id);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':location', $location);
                $stmt->bindParam(':img_url', $img_url);
                $stmt->execute();
            }
        }
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}
