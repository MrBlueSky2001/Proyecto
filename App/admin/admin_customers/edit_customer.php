<?php
require_once '../../db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $role = $_POST['role']; 
    
    $stmt = $conn->prepare("UPDATE customer SET username = ?, phone_number = ?, address = ?, role = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $username, $phone_number, $address, $role, $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
