<?php
require_once '../../db_config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

$id = $_GET['id'];

if (empty($id)) {
    echo json_encode(['error' => 'ID no proporcionado']);
    exit();
}

$stmt = $conn->prepare("DELETE FROM food WHERE id = ?");
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    echo json_encode(['success' => 'Comida eliminada con éxito']);
} else {
    echo json_encode(['error' => 'Error al eliminar la comida']);
}

$stmt->close();
$conn->close();
