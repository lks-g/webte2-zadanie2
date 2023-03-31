<?php

require_once('../config.php');

function getTextBetweenTags($tag, $html) {
    $pattern = "/<$tag.*?>(.*?)<\/$tag>/s";
    preg_match($pattern, $html, $matches);
    return $matches[1];
}

function getListItems($html) {
    $pattern = "/<li[^>]*>(.*?)<\/li>/s";
    preg_match_all($pattern, $html, $matches);
    return $matches[1];
}

function downloadImage($url, $filename) {
    $ch = curl_init($url);
    $fp = fopen($filename, 'wb');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
}

try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT * FROM menus WHERE source_code IS NOT NULL ORDER BY download_time DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $menuRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($menuRow) {
        $provider_id = $menuRow['provider_id'];
        $source_code = $menuRow['source_code'];
   
        if (strpos($source_code, 'menu-listing') !== false) {
            $menuSection = 'Venza';
            $menuHtml = getTextBetweenTags('ul', $source_code);
        } elseif (strpos($source_code, 'menu-body') !== false) {
            $menuSection = 'Eat&Meet';
            $menuHtml = getTextBetweenTags('div', $source_code);
        } elseif (strpos($source_code, 'day-title') !== false) {
            $menuSection = 'FreeFood';
            $menuHtml = $source_code;
        } else {
            throw new Exception("Menu section not found.");
        }
  
        switch ($menuSection) {
            case 'Venza':
                $menuItems = getListItems($menuHtml);
                foreach ($menuItems as $menuItemHtml) {
                    $name = trim(getTextBetweenTags('h5', $menuItemHtml));
                    $description = trim(getTextBetweenTags('p', $menuItemHtml));
                    $price = null;
                    $priceHtml = getTextBetweenTags('h5', $menuItemHtml, 1);
                    if ($priceHtml) {
                        $priceParts = explode('/', $priceHtml);
                        $price = trim(str_replace('€', '', $priceParts[0]));
                    }
                    $db->beginTransaction();
                    $stmt = $db->prepare("INSERT INTO menu_items (provider_id, name, description, price) VALUES (?, ?, ?, ?)");
                    $stmt->execute(array($provider_id, $name, $description, $price));
                    $menuItemID = $db->lastInsertId();
                    $db->commit();
                }
                break;

            case 'Eat&Meet':
                preg_match_all('#<div class="menu-body menu-left  menu-white ">(.*?)</div>#s', $menuHtml, $menuItemsHtml);
                foreach ($menuItemsHtml[0] as $menuItemHtml) {
                    $name = trim(getTextBetweenTags('h4', $menuItemHtml));
                    $description = trim(getTextBetweenTags('p', $menuItemHtml, 1));
                    $price = null;
                    $priceHtml = getTextBetweenTags('span', $menuItemHtml);
                    if ($priceHtml) {
                        $priceParts = explode('/', $priceHtml);
                        $price = trim(str_replace('€', '', $priceParts[0]));
                    }
                    $imageHtml = getTextBetweenTags('img', $menuItemHtml);
                    if ($imageHtml) {
                        preg_match('/src="([^"]*)"/i', $imageHtml, $imageSrc);
                        if (!empty($imageSrc[1])) {
                            $imageFilename = 'menu_item_' . $menuItemID . '.png';
                            downloadImage($imageSrc[1], $imageFilename);
                        }
                    }
                    $db->beginTransaction();
                    $stmt = $db->prepare("INSERT INTO menu_items (provider_id, name, description, price, image_filename) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute(array($provider_id, $name, $description, $price, $imageFilename));
                    $menuItemID = $db->lastInsertId();
                    $db->commit();
                }
                break;

            case 'FreeFood':
                preg_match_all('#<li><span class="brand">(.*?)</span>(.*?)</li>#s', $menuHtml, $menuItemsHtml);
                foreach ($menuItemsHtml[0] as $menuItemHtml) {
                    $name = trim(getTextBetweenTags('span', $menuItemHtml));
                    $description = trim(getTextBetweenTags('span', $menuItemHtml, 1));
                    $price = null;
                    $priceHtml = getTextBetweenTags('span', $menuItemHtml, 2);
                    if ($priceHtml) {
                        $price = trim(str_replace('€', '', $priceHtml));
                    }
                    $db->beginTransaction();
                    $stmt = $db->prepare("INSERT INTO menu_items (provider_id, name, description, price) VALUES (?, ?, ?, ?)");
                    $stmt->execute(array($provider_id, $name, $description, $price));
                    $menuItemID = $db->lastInsertId();
                    $db->commit();
                }
                break;

            default:
                throw new Exception("Menu section not found.");
            }
        }
    } catch (PDOException $e) {
    echo $e->getMessage();
}