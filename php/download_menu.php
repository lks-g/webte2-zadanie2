<?php

require_once('../config.php');

try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT id, url FROM providers WHERE name = 'FreeFood' OR name = 'VENZA' OR name = 'Eat&Meet'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $menuData = array();

    foreach ($rows as $row) {
        $provider_id = $row['id'];
        $url = $row['url'];
  
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);

        $date = date("Y-m-d");
        $sql = "INSERT INTO menus (provider_id, menu_date, download_time) VALUES (:provider_id, :menu_date, NOW())";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':provider_id', $provider_id);
        $stmt->bindParam(':menu_date', $date);
        $stmt->execute();

        $menu_id = $db->lastInsertId();

        $menuData[] = array(
            'menu_id' => $menu_id,
            'provider_id' => $provider_id,
            'menu_date' => $date,
            'menu_data' => json_decode($output, true)
        );
    }

    $json = json_encode($menuData);
    file_put_contents('../json/menu.json', $json);

} catch (PDOException $e) {
    echo $e->getMessage();
    exit();
}
?>