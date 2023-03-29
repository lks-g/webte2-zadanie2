<?php

require_once('../config.php');

try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the last downloaded menu record
    $sql = "SELECT * FROM menus WHERE source_code IS NOT NULL ORDER BY download_time DESC LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $menuRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($menuRow) {
        // Get the provider ID for the downloaded menu
        $provider_id = $menuRow['provider_id'];

        // Parse the menu data
        $source_code = $menuRow['source_code'];
        $doc = new DOMDocument();
        @$doc->loadHTML($source_code);

        // Find the menu section
        $menuSection = null;
        $menuList = array();
        $dayTabs = $doc->getElementById('pills-tab')->childNodes;
        foreach ($dayTabs as $dayTab) {
            if ($dayTab->nodeType == XML_ELEMENT_NODE) {
                $dayId = $dayTab->getAttribute('aria-controls');
                if (strpos($dayId, 'day_') !== false) {
                    $dayDiv = $doc->getElementById($dayId);
                    if ($dayDiv) {
                        $menuSection = $dayDiv->getElementsByTagName('div')->item(0);
                        break;
                    }
                }
            }
        }

        // Parse the menu items
        if ($menuSection) {
            $menuItems = $menuSection->getElementsByTagName('li');
            foreach ($menuItems as $menuItem) {
                $nameNode = $menuItem->getElementsByTagName('h5')->item(0);
                $name = $nameNode->textContent;

                $priceNode = $menuItem->getElementsByTagName('h5')->item(1);
                $priceText = $priceNode->textContent;
                preg_match_all('/[0-9]+\,[0-9]+/', $priceText, $matches);
                $price = $matches[0][0];

                $locationNode = $menuItem->getElementsByTagName('p')->item(0);
                $location = $locationNode->textContent;

                $imageNode = $menuItem->getElementsByTagName('img')->item(0);
                $image_url = $imageNode ? $imageNode->getAttribute('src') : null;

                // Check if the dish already exists in the database
                $sql = "SELECT * FROM dishes WHERE menu_id = :menu_id AND name = :name";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':menu_id', $menuRow['menu_id']);
                $stmt->bindParam(':name', $name);
                $stmt->execute();
                $existingDish = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$existingDish) {
                    $sql = "INSERT INTO dishes (menu_id, name, price, location, image_url) VALUES (:menu_id, :name, :price, :location, :image_url)";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':menu_id', $menuRow['menu_id']);
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':price', $price);
                    $stmt->bindParam(':location', $location);
                    $image_url = null;
                    $stmt->bindParam(':image_url', $image_url);
                    $stmt->execute();
                }
            }
        }
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}

?>