<?php
require_once '../../db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];

    $stmt = $conn->prepare("UPDATE restaurant SET name = ?, address = ?, phone_number = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $address, $phone_number, $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
