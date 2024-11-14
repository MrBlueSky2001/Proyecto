<?php
session_start();
require_once '../../db_config.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../../login.php");
    exit();
}

$user = $_SESSION['user'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone_number = htmlspecialchars($_POST['phone_number']);
    $address = htmlspecialchars($_POST['address']);

    $stmt = $conn->prepare("UPDATE customer SET phone_number = ?, address = ? WHERE id = ?");
    $stmt->bind_param("ssi", $phone_number, $address, $user['id']);

    if ($stmt->execute()) {
        $success = "Perfil actualizado con éxito.";
        $user['phone_number'] = $phone_number;
        $user['address'] = $address;
        $_SESSION['user'] = $user;
    } else {
        $error = "Error al actualizar el perfil.";
    }
    $stmt->close();
}
?>
