<?php
require_once '../../db_config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM restaurant WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header('Location: admin_restaurants.php');
        exit();
    }
}
?>
