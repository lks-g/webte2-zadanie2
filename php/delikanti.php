<?php

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://www.delikanti.sk/prevadzky/3-jedalen-prif-uk/");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$output = curl_exec($ch);

curl_close($ch);

$dom = new DOMDocument();

$dom->loadHTML($output, LIBXML_NOWARNING | LIBXML_NOERROR);

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

echo json_encode($dishes, JSON_UNESCAPED_UNICODE);
