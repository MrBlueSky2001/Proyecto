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

if (empty($id) || empty($name) || empty($description)) {
    echo json_encode(['error' => 'Todos los campos son obligatorios']);
    exit();
}

$stmt = $conn->prepare("UPDATE food SET name = ?, description = ? WHERE id = ?");
$stmt->bind_param('ssi', $name, $description, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => 'Comida actualizada con éxito']);
} else {
    echo json_encode(['error' => 'Error al actualizar la comida']);
}

$stmt->close();
$conn->close();
