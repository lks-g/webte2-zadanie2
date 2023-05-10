<?php

require_once('../config.php');

try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $provider_id = 2;
    $stmt = $db->prepare("SELECT source_code FROM menus WHERE provider_id = :provider_id");
    $stmt->bindParam(':provider_id', $provider_id, PDO::PARAM_INT);
    $stmt->execute();
    $menu = $stmt->fetchColumn();

} catch (PDOException $e) {
    echo "Error retrieving menu: " . $e->getMessage();
}

$dom = new DOMDocument();
@$dom->loadHTML($menu, LIBXML_NOWARNING | LIBXML_NOERROR);
$dom->preserveWhiteSpace = false;

$tables = $dom->getElementsByTagName('table');
$rows = $tables->item(0)->getElementsByTagName('tr');
$index = 0;
$dayCount = 0;

$dishes = [];
$foodCount = $rows->item(0)->getElementsByTagName('th')->item(0)->getAttribute('rowspan');

foreach ($rows as $row) {

    if ($row->getElementsByTagName('th')->item(0)) {
        $foodCount = $row->getElementsByTagName('th')->item(0)->getAttribute('rowspan');

        $dayNode = $rows->item($index)->getElementsByTagName('th')->item(0)->getElementsByTagName('strong')->item(0);
        $day = $dayNode ? trim($dayNode->nodeValue) : null;

        $th = $rows->item($index)->getElementsByTagName('th')->item(0);

        foreach ($th->childNodes as $node)
            if (!($node instanceof \DomText))
                $node->parentNode->removeChild($node);

        $date = trim($rows->item($index)->getElementsByTagName('th')->item(0)->nodeValue);

        $dishes[] = ["date" => $date, "day" => $day, "menu" => []];

        for ($i = $index; $i < $index + intval($foodCount); $i++) {
            if ($dishes[$dayCount]) {
                $menuItemNode = $rows->item($i)->getElementsByTagName('td')->item(1);
                $menuItem = $menuItemNode ? trim($menuItemNode->nodeValue) : null;
                $dishes[$dayCount]["menu"][] = $menuItem;
            }
        }
        $index += intval($foodCount);
        $dayCount++;
    }
}

$data = json_encode($dishes, JSON_UNESCAPED_UNICODE);

$sql = "INSERT INTO dishes (menu_id, parsed_data, download_date) VALUES (:menu_id, :parsed_data, :download_date)
ON DUPLICATE KEY UPDATE 
  parsed_data = :parsed_data, 
  download_date = :download_date
";
$stmt = $db->prepare($sql);
$stmt->bindValue(':menu_id', 2, PDO::PARAM_INT);
$stmt->bindValue(':parsed_data', $data);
$stmt->bindValue(':download_date', date('Y-m-d H:i:s'));
$stmt->execute();

$db = null;
echo $data;
