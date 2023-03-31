<?php

require_once('../config.php');

try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "DROP TABLE IF EXISTS dishes";
    $stmt = $db->prepare($sql);
    $stmt->execute();

    $sql = "DROP TABLE IF EXISTS menus";
    $stmt = $db->prepare($sql);
    $stmt->execute();

    echo "Tables deleted successfully.";

} catch (PDOException $e) {
    echo "Error deleting tables: " . $e->getMessage();
}

?>
