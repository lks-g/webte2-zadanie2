<?php

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://www.freefood.sk/menu/#fiit-food");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);
curl_close($ch);
$dom = new DOMDocument();
@$dom->loadHTML($output);
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

echo json_encode($dishes, JSON_UNESCAPED_UNICODE);
