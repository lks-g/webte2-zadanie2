<?php

require_once('../config.php');

try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname;charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT provider_id, url FROM providers WHERE name = 'FreeFood' OR name = 'Delikanti' OR name = 'Eat&Meet'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $menuData = array();

    foreach ($rows as $row) {
        $provider_id = $row['provider_id'];
        $url = $row['url'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);

        $menuData[] = array(
            'provider_id' => $provider_id,
            'menu_date' => date('Y-m-d'),
            'source_code' => $output,
            'download_date' => date('Y-m-d H:i:s')
        );
    }

    $sql = "INSERT INTO menus (provider_id, menu_date, source_code, download_date) VALUES (:provider_id, :menu_date, :source_code, :download_date) 
    ON DUPLICATE KEY UPDATE source_code = VALUES(source_code), download_date = VALUES(download_date)";
    $stmt = $db->prepare($sql);

    foreach ($menuData as $data) {
        $stmt->bindParam(':provider_id', $data['provider_id']);
        $stmt->bindParam(':menu_date', $data['menu_date']);
        $stmt->bindParam(':source_code', $data['source_code']);
        $stmt->bindParam(':download_date', $data['download_date']);
        $stmt->execute();

        $menu_id = $db->lastInsertId();
    }
} catch(PDOException $e) {
    echo $e->getMessage();
}
