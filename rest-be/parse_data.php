<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../config.php');

function parse_freefood($menu_source_code)
{
    $menu_type = null;

    $dom = new DOMDocument();
    @$dom->loadHTML(mb_convert_encoding($menu_source_code, 'HTML-ENTITIES', 'UTF-8'));

    $menu_type_node = $dom->getElementsByTagName("h2")->item(0);
    if ($menu_type_node) {
        $menu_type = trim($menu_type_node->nodeValue);
        if (empty($menu_type)) {
            throw new Exception("No menu type specified");
        }
    } else {
        throw new Exception("Menu type not found");
    }
    $menu_items = $dom->getElementsByTagName("tr");
    $date = date('Y-m-d H:i:s');
    $dishes = array();
    foreach ($menu_items as $menu_item) {
        $name_node = $menu_item->getElementsByTagName("td")->item(0);
        $price_node = $menu_item->getElementsByTagName("td")->item(1);
        $location = "Fakulta informatiky a informačných technológií STU, Ilkovičova 2, 841 04 Karlova Ves";
        $image_url = null;
        $name = ($name_node) ? trim($name_node->nodeValue) : null;
        $price = ($price_node) ? trim($price_node->nodeValue) : null;
        if (empty($name) || empty($price)) {
            throw new Exception("Name or Price is empty");
        }

        $dishes[] = array(
            'name' => $name,
            'menu_type' => $menu_type,
            'price' => $price,
            'location' => $location,
            'image_url' => $image_url,
            'menu_date' => $date
        );
    }
    return $dishes;
}

function parse_venza($menu_source_code)
{
    $menu_type = null;
    $dom = new DOMDocument();
    @$dom->loadHTML(mb_convert_encoding($menu_source_code, 'HTML-ENTITIES', 'UTF-8'));

    $menu_type_node = $dom->getElementsByTagName("h5")->item(0);
    if ($menu_type_node) {
        $menu_type = trim($menu_type_node->nodeValue);
        if (empty($menu_type)) {
            throw new Exception("No menu type specified");
        }
    } else {
        throw new Exception("Menu type not found");
    }

    $menu_items = $dom->getElementsByTagName("li");
    $date = date('Y-m-d H:i:s');
    $dishes = array();
    foreach ($menu_items as $menu_item) {
        $name_node = $menu_item->getElementsByTagName("h5")->item(0);
        $price_node = $menu_item->getElementsByTagName("h5")->item(1);
        $location_nodes = $dom->getElementsByTagName("p");
        $image_url_node = $menu_item->getElementsByTagName("img")->item(0);
        $name = ($name_node) ? trim($name_node->nodeValue) : null;
        $price = ($price_node) ? trim($price_node->nodeValue) : null;
        $location = null;
        if (count($location_nodes) >= 2) {
            $location = trim($location_nodes->item(0)->nodeValue) . ', ' . trim($location_nodes->item(1)->nodeValue);
        }
        $image_url = ($image_url_node) ? trim($image_url_node->getAttribute('src')) : null;
        if (empty($name) || empty($price) || empty($location)) {
            throw new Exception("Name, Price or Location is empty");
        }

        $dish = array(
            'name' => $name,
            'menu_type' => $menu_type,
            'price' => $price,
            'location' => $location,
            'image_url' => $image_url,
            'menu_date' => $date
        );
        array_push($dishes, $dish);
    }
    return $dishes;
}

function parse_eatAndMeet($menu_source_code)
{
    $dom = new DOMDocument();
    if (!$dom->loadHTML(mb_convert_encoding($menu_source_code, 'HTML-ENTITIES', 'UTF-8'))) {
        throw new Exception("Error loading HTML");
    }

    $xpath = new DOMXPath($dom);
    $days = ['Pondelok', 'Utorok', 'Streda', 'Štvrtok', 'Piatok', 'Sobota', 'Nedeľa'];
    $menu_date = $days[date('N') - 1];

    if (!$menu_date) {
        throw new Exception("No active menu found for the given date");
    }

    $dishes = [];

    foreach ($xpath->query('//div[contains(@class, "menu-body")]') as $menu_item) {
        $price = extract_text($xpath->query('.//span[@class="price"]', $menu_item)->item(0));
        $price = floatval(str_replace(',', '.', $price));

        $image_url = $xpath->evaluate('string(.//img/@src)', $menu_item);

        $menu_type = extract_text($xpath->query('.//h4', $menu_item)->item(0));
        $menu_type = trim(str_replace('MENU', '', $menu_type));

        $name = extract_text($xpath->query('.//p[@class="desc"]', $menu_item)->item(0));
        $name = trim(preg_replace('/\(\d+(,\d+)*\)/', '', $name));

        $dishes[] = [
            'menu_type' => $menu_type,
            'name' => $name,
            'price' => $price,
            'image_url' => $image_url,
            'location' => 'Staré Grunty 36, Átriaky, Blok AD-U, 841 04 Karlova Ves, Slovakia',
            'menu_date' => date('Y-m-d H:i:s')
        ];
    }
    return $dishes;
}

function extract_text($node) {
    return preg_replace('/\s+/', ' ', trim($node->textContent));
}

try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname;charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT * FROM menus WHERE source_code IS NOT NULL ORDER BY download_date DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute();

    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($menus as $menu) {
        switch ($menu['provider_id']) {
            case 1:
                $dishes = parse_freefood($menu['source_code']);
                break;
            case 3:
                $dishes = parse_eatAndMeet($menu['source_code']);
                break;
            case 2:
                $dishes = parse_venza($menu['source_code']);
                break;
            default:
                echo "Error: Provider does not exist.";
                break;
        }

        foreach ($dishes as $dish) {
            $stmt = $pdo->prepare("INSERT INTO dishes (menu_id, menu_type, name, price, location, image_url, menu_date) VALUES (:menu_id, :menu_type, :name, :price, :location, :image_url, :menu_date)");
            $stmt->execute([
                'menu_id' => $menu['id'],
                'menu_type' => $dish['menu_type'],
                'name' => $dish['name'],
                'price' => $dish['price'],
                'location' => $dish['location'],
                'image_url' => $dish['image_url'],
                'menu_date' => date('Y-m-d H:i:s')
            ]);
        }
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}
