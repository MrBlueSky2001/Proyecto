<?php
    // Incluimos la configuración de la base de datos
    require_once '../../db_config.php';

    // Verificamos si la solicitud se ha realizado mediante el método POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtemos el cuerpo de la solicitud y lo decodifica desde JSON a un array asociativo
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Verificamos si se ha proporcionado un ID en los datos de la solicitud
        if (isset($data['id'])) {
            $id = $data['id']; // Almacenamos el ID proporcionado

            // Preparamos la consulta SQL para eliminar el método de pago con el ID especificado
            $query = "DELETE FROM PaymentMethod WHERE id = ?";
            $stmt = $conn->prepare($query); // Preparamos la declaración
            $stmt->bind_param('i', $id); // Vinculamos el parámetro ID como entero

            // Ejecutamos la consulta
            if ($stmt->execute()) {
                // Si la ejecución ha tenido éxito, devuelve un JSON con éxito
                echo json_encode(['success' => true]);
            } else {
                // Si hay un error al ejecutar la consulta, devolvemos un JSON con un mensaje de error
                echo json_encode(['success' => false, 'message' => 'Error al eliminar el método de pago']);
            }
        } else {
            // Si no se proporciona un ID, devolvemos un JSON con un mensaje de error
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
        }
    } else {
        // Si el método de la solicitud no es POST, devolvemos un JSON con un mensaje de error
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
?>