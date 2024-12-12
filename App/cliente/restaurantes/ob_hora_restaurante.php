<?php
    // Incluimos la configuración de la base de datos
    require_once '../../db_config.php';

    // Verificamos si la solicitud es de tipo GET
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Obtenemos el ID del restaurante de los parámetros de la URL
        $restaurantId = $_GET['restaurant_id'];

        // Preparamos la consulta SQL para obtener los horarios de apertura y cierre del restaurante
        $stmt = $conn->prepare("SELECT open_time, close_time FROM Restaurant WHERE id = ?");
        $stmt->bind_param("i", $restaurantId); // Vinculamos el ID del restaurante como un entero
        $stmt->execute(); // Ejecutamos la consulta
        $result = $stmt->get_result(); // Obtenemos el resultado de la consulta

        // Verificamos si se encontró un restaurante con el ID proporcionado
        if ($row = $result->fetch_assoc()) {
            // Si se encuentra, devolvemos los horarios en formato JSON
            echo json_encode(['open_time' => $row['open_time'], 'close_time' => $row['close_time']]);
        } else {
            // Si no se encuentra, devolvemos un mensaje de error en formato JSON
            echo json_encode(['error' => 'Restaurante no encontrado']);
        }

        $stmt->close();
    }
?>