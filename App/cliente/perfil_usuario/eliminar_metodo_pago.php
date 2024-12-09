<?php
    require_once '../../db_config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtener el cuerpo de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['id'])) {
            $id = $data['id'];

            $query = "DELETE FROM PaymentMethod WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $id);

            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar el método de pago']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
?>