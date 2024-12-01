<?php
    require_once '../../db_config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $restaurantId = $_GET['restaurant_id'];

        $stmt = $conn->prepare("SELECT open_time, close_time FROM Restaurant WHERE id = ?");
        $stmt->bind_param("i", $restaurantId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            echo json_encode(['open_time' => $row['open_time'], 'close_time' => $row['close_time']]);
        } else {
            echo json_encode(['error' => 'Restaurante no encontrado']);
        }

        $stmt->close();
    }
?>