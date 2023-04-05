<?php

require_once('../config.php');

try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SET FOREIGN_KEY_CHECKS = 0";
    $stmt = $db->prepare($sql);
    $stmt->execute();

    $sql = "TRUNCATE TABLE dishes";
    $stmt = $db->prepare($sql);
    $stmt->execute();

    $sql = "TRUNCATE TABLE menus";
    $stmt = $db->prepare($sql);
    $stmt->execute();

    $sql = "SET FOREIGN_KEY_CHECKS = 1";
    $stmt = $db->prepare($sql);
    $stmt->execute();
} catch (PDOException $e) {
    echo "Error truncating tables: " . $e->getMessage();
}

?>
