<?php
    session_start();
    require_once '../../db_config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_SESSION['user']['id'])) {
            echo "Error: Usuario no autenticado.";
            exit;
        }

        $customer_id = $_SESSION['user']['id'];
        $reservation_id = $_POST['reservation_id'];
        $foods = $_POST['foods'];
        $payment_method_id = $_POST['payment_method_id'];

        // Crear el pedido anticipado con método de pago
        $stmt = $conn->prepare("
            INSERT INTO PreOrder (customer_id, reservation_id, status, payment_method_id)
            VALUES (?, ?, 'pendiente', ?)
        ");
        $stmt->bind_param("iii", $customer_id, $reservation_id, $payment_method_id);

        if (!$stmt->execute()) {
            echo "Error al crear el pedido anticipado: " . $stmt->error;
            exit;
        }

        $preorder_id = $stmt->insert_id;

        // Asignar comidas al pedido
        $stmt = $conn->prepare("
            INSERT INTO PreOrder_Food (preorder_id, food_id)
            VALUES (?, ?)
        ");
        foreach ($foods as $food_id) {
            $stmt->bind_param("ii", $preorder_id, $food_id);
            $stmt->execute();
        }

        echo "Pedido anticipado guardado con éxito.";
    }
?>
