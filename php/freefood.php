<?php

require_once('../config.php');

try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $provider_id = 1;
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

$dishes = [
    ["date" => date('d.m.Y', strtotime('monday this week')), "day" => "Pondelok", "menu" => []],
    ["date" => date('d.m.Y', strtotime('tuesday this week')), "day" => "Utorok", "menu" => []],
    ["date" => date('d.m.Y', strtotime('wednesday this week')), "day" => "Streda", "menu" => []],
    ["date" => date('d.m.Y', strtotime('thursday this week')), "day" => "Štvrtok", "menu" => []],
    ["date" => date('d.m.Y', strtotime('friday this week')), "day" => "Piatok", "menu" => []],
    ["date" => date('d.m.Y', strtotime('saturday this week')), "day" => "Sobota", "menu" => []],
    ["date" => date('d.m.Y', strtotime('sunday this week')), "day" => "Nedeľa", "menu" => []],
];

$ind = 0;
$lis = $dom->getElementById("fiit-food")->getElementsByTagName("li");
foreach ($lis as $i => $li) {
    if ($i % 5 != 0 && isset($li->childNodes->item(1)->nodeValue) && isset($li->childNodes->item(2)->nodeValue)) {
        $dishes[$ind]["menu"][] = $li->childNodes->item(1)->nodeValue . ": " . $li->childNodes->item(2)->nodeValue;
    } elseif ($i % 5 == 0 && $i != 0) {
        $ind++;
    }
    if ($i == 25) {
        break; 
    }
}

$data = json_encode($dishes, JSON_UNESCAPED_UNICODE);

$sql = "INSERT INTO dishes (menu_id, parsed_data, download_date) VALUES (:menu_id, :parsed_data, :download_date)
ON DUPLICATE KEY UPDATE 
  parsed_data = :parsed_data, 
  download_date = :download_date
";
$stmt = $db->prepare($sql);
$stmt->bindValue(':menu_id', 1, PDO::PARAM_INT);
$stmt->bindValue(':parsed_data', $data);
$stmt->bindValue(':download_date', date('Y-m-d H:i:s'));
$stmt->execute();

$db = null;
echo $data;
