<?php

require_once('../config.php');

try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $provider_id = 3;
    $stmt = $db->prepare("SELECT source_code FROM menus WHERE provider_id = :provider_id");
    $stmt->bindParam(':provider_id', $provider_id, PDO::PARAM_INT);
    $stmt->execute();
    $menu = $stmt->fetchColumn();

} catch (PDOException $e) {
    echo "Error retrieving menu: " . $e->getMessage();
}

$dom = new DOMDocument();
@$dom->loadHTML($menu);
$dom->preserveWhiteSpace = false;

$parseNodes = ["day-1", "day-2", "day-3", "day-4", "day-5", "day-6", "day-7"];

$dishes = [
    ["date"  => date( 'd.m.Y', strtotime( 'monday this week' ) ), "day" => "Pondelok", "menu" => []],
    ["date"  => date( 'd.m.Y', strtotime( 'tuesday this week' ) ), "day" => "Utorok", "menu" => []],
    ["date"  => date( 'd.m.Y', strtotime( 'wednesday this week' ) ), "day" => "Streda", "menu" => []],
    ["date"  => date( 'd.m.Y', strtotime( 'thursday this week' ) ), "day" => "Štvrtok", "menu" => []],
    ["date"  => date( 'd.m.Y', strtotime( 'friday this week' ) ), "day" => "Piatok", "menu" => []],
    ["date"  => date( 'd.m.Y', strtotime( 'saturday this week' ) ), "day" => "Sobota", "menu" => []],
    ["date"  => date( 'd.m.Y', strtotime( 'sunday this week' ) ), "day" => "Nedeľa", "menu" => []],
];

foreach ($parseNodes as $index => $nodeId) {

    $node = $dom->getElementById($nodeId);

    foreach ($node->childNodes as $menuItem)
    {
        if($menuItem && $menuItem->childNodes->item(1) && $menuItem->childNodes->item(1)->childNodes->item(3)){
            $meal = trim($menuItem->childNodes->item(1)->childNodes->item(3)->childNodes->item(1)->childNodes->item(1)->nodeValue);
            $price = trim($menuItem->childNodes->item(1)->childNodes->item(3)->childNodes->item(1)->childNodes->item(3)->nodeValue);
            $description = trim($menuItem->childNodes->item(1)->childNodes->item(3)->childNodes->item(3)->nodeValue);
            array_push($dishes[$index]["menu"], "$meal($description): $price");
        }
    }
}

$data = json_encode($dishes, JSON_UNESCAPED_UNICODE);

$sql = "INSERT INTO dishes (menu_id, parsed_data, download_date) VALUES (:menu_id, :parsed_data, :download_date)
ON DUPLICATE KEY UPDATE 
  parsed_data = :parsed_data, 
  download_date = :download_date
";
$stmt = $db->prepare($sql);
$stmt->bindValue(':menu_id', 3, PDO::PARAM_INT);
$stmt->bindValue(':parsed_data', $data);
$stmt->bindValue(':download_date', date('Y-m-d H:i:s'));
$stmt->execute();

$db = null;
echo $data;
