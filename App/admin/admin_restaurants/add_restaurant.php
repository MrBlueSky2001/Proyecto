<?php
require_once '../../db_config.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];

    $stmt = $conn->prepare("INSERT INTO restaurant (name, address, phone_number) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $address, $phone_number);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
}
?>
