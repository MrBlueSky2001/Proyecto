<?php
require_once '../../db_config.php';
session_start();

// Configuración para responder con JSON
header('Content-Type: application/json');

// Verificar si el usuario está autenticado y es admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['error' => 'No tienes permisos para realizar esta acción.']);
    exit();
}

// Obtener los datos del formulario
$name = isset($_POST['name']) ? $_POST['name'] : '';
$description = isset($_POST['description']) ? $_POST['description'] : '';
$restaurant_id = isset($_POST['restaurant_id']) ? $_POST['restaurant_id'] : '';
$category_id = isset($_POST['category_id']) ? $_POST['category_id'] : '';
$price = isset($_POST['price']) ? $_POST['price'] : '';

// Validar que los campos necesarios no estén vacíos
if (empty($name) || empty($description) || empty($restaurant_id) || empty($category_id) || empty($price)) {
    echo json_encode(['error' => 'Todos los campos son obligatorios']);
    exit();
}

// Preparar la consulta para insertar la comida
$stmt = $conn->prepare("INSERT INTO food (name, description, price, restaurant_id, category_id) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param('ssdii', $name, $description, $price, $restaurant_id, $category_id);

// Ejecutar la consulta
if ($stmt->execute()) {
    echo json_encode(['success' => 'Comida añadida con éxito']);
} else {
    // Si ocurre un error, se retorna el error
    echo json_encode(['error' => 'Error al añadir la comida: ' . $stmt->error]);
}

// Cerrar la conexión y la consulta
$stmt->close();
$conn->close();