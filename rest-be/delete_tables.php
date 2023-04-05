<?php

require_once('../config.php');

try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "TRUNCATE TABLE dishes";
    $stmt = $db->prepare($sql);
    $stmt->execute();

    $sql = "TRUNCATE TABLE menus";
    $stmt = $db->prepare($sql);
    $stmt->execute();

    echo "Tables truncated successfully.";

} catch (PDOException $e) {
    echo "Error truncating tables: " . $e->getMessage();
}

?>
