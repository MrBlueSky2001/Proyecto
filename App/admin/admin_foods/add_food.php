<?php
require_once '../../db_config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

$name = $_POST['name'];
$description = $_POST['description'];
$restaurant_id = $_POST['restaurant_id'];

if (empty($name) || empty($description) || empty($restaurant_id)) {
    echo json_encode(['error' => 'Todos los campos son obligatorios']);
    exit();
}

$stmt = $conn->prepare("INSERT INTO food (name, description, restaurant_id) VALUES (?, ?, ?)");
$stmt->bind_param('ssi', $name, $description, $restaurant_id);

if ($stmt->execute()) {
    echo json_encode(['success' => 'Comida añadida con éxito']);
} else {
    echo json_encode(['error' => 'Error al añadir la comida']);
}

$stmt->close();
$conn->close();
