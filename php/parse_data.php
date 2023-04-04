<?php

require_once('../config.php');

function parse_freefood_menu($menu_source_code) {
    $dom = new DOMDocument();
    $dom->loadHTML($menu_source_code);

    $menu_type = $dom->getElementsByTagName("h4")->item(0)->nodeValue;
    $menu_items = $dom->getElementsByTagName("li");

    $dishes = array();
    foreach ($menu_items as $menu_item) {
        $day_title = $menu_item->getElementsByTagName("span")->item(0)->nodeValue;
        $date = DateTime::createFromFormat('l, j.n.Y', $day_title);
        if (!$date) {
            continue;
        }
        $offer_items = $menu_item->getElementsByTagName("li");
        foreach ($offer_items as $offer_item) {
            $name = $offer_item->getElementsByTagName("span")->item(1)->nodeValue;
            $price_node = $offer_item->getElementsByTagName("span")->item(2);
            $price = trim(str_replace('€', '', $price_node->nodeValue));
            $dishes[] = array(
                'menu_type' => $menu_type,
                'name' => $name,
                'price' => $price,
                'location' => 'FreeFood',
                'image_url' => null
            );
        }
    }
    return $dishes;
}

function parse_venza_menu($menu_source_code) {
    $dom = new DOMDocument();
    $dom->loadHTML($menu_source_code);

    $menu_type = $dom->getElementsByTagName("h5")->item(0)->nodeValue;
    $menu_items = $dom->getElementsByTagName("li");

    $dishes = array();
    foreach ($menu_items as $menu_item) {
        $name = $menu_item->getElementsByTagName("h5")->item(0)->nodeValue;
        $price = $menu_item->getElementsByTagName("h5")->item(1)->nodeValue;
        $location = $dom->getElementsByTagName("p")->item(0)->nodeValue . ', ' . $dom->getElementsByTagName("p")->item(1)->nodeValue;
        $dishes[] = array(
            'menu_type' => $menu_type,
            'name' => $name,
            'price' => $price,
            'location' => $location,
            'image_url' => null
        );
    }
    return $dishes;
}

function parse_eatmeet_menu($menu_source_code) {
    $dom = new DOMDocument();
    $dom->loadHTML($menu_source_code);

    $menu_type = $dom->getElementsByTagName("h4")->item(0)->nodeValue;
    $menu_items = $dom->getElementsByTagName("div");

    $dishes = array();

    $location_items = $dom->getElementsByTagName("div");
    $location = "";
    foreach ($location_items as $location_item) {
        if ($location_item->getAttribute('class') == 'contact') {
            $location_nodes = $location_item->getElementsByTagName("p");
            $location_arr = array();
            foreach ($location_nodes as $location_node) {
                $location_arr[] = trim($location_node->nodeValue);
            }
            $location = implode(", ", array_slice($location_arr, 0, 2));
            break;
        }
    }

    foreach ($menu_items as $menu_item) {
        if ($menu_item->getAttribute('class') == 'menu-body menu-left  menu-white ') {
            $name = trim($menu_item->getElementsByTagName("p")->item(0)->nodeValue);
            $price = $menu_item->getElementsByTagName("span")->item(0)->nodeValue;
            $img_node = $menu_item->getElementsByTagName("img")->item(0);
            $img_url = $img_node ? $img_node->getAttribute('src') : null;

            $dishes[] = array(
                'menu_type' => $menu_type,
                'name' => $name,
                'price' => $price,
                'location' => $location,
                'image_url' => $img_url
            );
        }
    }
    return $dishes;
}

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

foreach ($menus as $menu) {
    switch ($menu['name']) {
        case "FreeFood":
            $freefood_dishes = parse_freefood_menu($menu['source_code']);
            $dishes = array_merge($dishes, $freefood_dishes);
            break;
        case "Eat&Meet":
            $eatmeet_dishes = parse_eatmeet_menu($menu['source_code']);
            $dishes = array_merge($dishes, $eatmeet_dishes);
            break;
        case "Venza":
            $venza_dishes = parse_venza_menu($menu['source_code']);
            $dishes = array_merge($dishes, $venza_dishes);
            break;
        default:
            echo "Error: Provider does not exist.";
            break;
    }
}
