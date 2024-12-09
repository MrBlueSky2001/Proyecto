<?php
require_once '../../db_config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

$id = $_POST['id'];
$name = $_POST['name'];
$description = $_POST['description'];
$price = $_POST['price'];  // Recibimos el precio

if (empty($id) || empty($name) || empty($description) || empty($price)) {
    echo json_encode(['error' => 'Todos los campos son obligatorios']);
    exit();
}

$stmt = $conn->prepare("UPDATE food SET name = ?, description = ?, price = ? WHERE id = ?");
$stmt->bind_param('ssdi', $name, $description, $price, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => 'Comida actualizada con éxito']);
} else {
    echo json_encode(['error' => 'Error al actualizar la comida']);
}

$stmt->close();
$conn->close();