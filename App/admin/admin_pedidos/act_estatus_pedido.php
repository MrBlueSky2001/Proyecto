<?php
    require_once '../../db_config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id']; // ID del pedido anticipado
        $status = $_POST['status']; // Nuevo estado

        // Preparar la consulta para actualizar el estado del pedido anticipado
        $stmt = $conn->prepare("UPDATE preorder SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);

        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }

        $stmt->close();
        $conn->close();
    }
?>