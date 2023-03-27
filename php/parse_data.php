<?php

require_once('../config.php');

try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT id FROM menus ORDER BY id DESC LIMIT 1";
    $stmt = $db->query($sql);
    $last_menu_id = $stmt->fetchColumn();

    $sql = "SELECT download_time, provider_id FROM menus WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $last_menu_id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $download_time = $row['download_time'];
    $provider_id = $row['provider_id'];

    $sql = "SELECT COUNT(*) AS count FROM menu_items WHERE menu_id = :menu_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':menu_id', $last_menu_id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = $row['count'];

    if ($count == 0) {
        $json_data = file_get_contents('../json/menu.json');
        $data = json_decode($json_data, true);

        $sql = "INSERT INTO menu_items (menu_id, item_name, item_description, item_price) VALUES (:menu_id, :item_name, :item_description, :item_price)";
        $stmt = $db->prepare($sql);
        foreach ($data as $item) {
            $stmt->bindParam(':menu_id', $last_menu_id);
            $stmt->bindParam(':item_name', $item['name']);
            $stmt->bindParam(':item_description', $item['description']);
            $stmt->bindParam(':item_price', $item['price']);
            $stmt->execute();
        }
    }

    echo 'Data parsed successfully';
} catch (PDOException $e) {
    echo $e->getMessage();
    exit();
}

?>