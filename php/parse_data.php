<?php

require_once('../config.php');

try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname;charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT * FROM menus WHERE source_code IS NOT NULL ORDER BY download_time DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute();

    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo $e->getMessage();
}

// Loop through the menus and parse the source code for each provider
foreach ($menus as $menu) {
    switch ($menu['provider_id']) {
        case 1: // FreeFood
            // Parse the source code for FreeFood
            // ...
            break;
        case 2: // Eat&Meet
            // Parse the source code for Eat&Meet
            // ...
            break;
        case 3: // Venza
            // Parse the source code for Venza
            $venza_dom = new DOMDocument();
            $venza_dom->loadHTML($menu['source_code']);

            // Find the menu items
            $venza_items = $venza_dom->getElementsByTagName("li");

            // Loop through the menu items and extract the name and price for each dish
            $venza_dishes = array();
            foreach ($venza_items as $venza_item) {
                $name = $venza_item->getElementsByTagName("h5")->item(0)->nodeValue;
                $price = $venza_item->getElementsByTagName("h5")->item(1)->nodeValue;
                $venza_dishes[] = array("name" => $name, "price" => $price);
            }

            // Find the location information
            $venza_location = $venza_dom->getElementsByTagName("div")->item(2)->getElementsByTagName("p")->item(0)->nodeValue;
            $venza_address = $venza_dom->getElementsByTagName("div")->item(2)->getElementsByTagName("p")->item(1)->nodeValue;

            // Output the results
            echo "Menu for Venza:\n";
            foreach ($venza_dishes as $dish) {
                echo $dish['name'] . " - " . $dish['price'] . "\n";
            }
            echo "Location for Venza: " . $venza_location . "\n";
            echo "Address for Venza: " . $venza_address . "\n";
            break;
        default:
            // Unsupported provider_id
            break;
    }
}
