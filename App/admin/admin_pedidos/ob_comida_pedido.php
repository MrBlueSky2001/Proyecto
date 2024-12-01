<?php
    require_once '../../db_config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $preorder_id = $_POST['preorder_id'];

        // Obtener las comidas del pedido anticipado
        $stmt = $conn->prepare("
            SELECT f.name, f.description, fc.category_name 
            FROM PreOrder_Food pf
            JOIN Food f ON pf.food_id = f.id
            JOIN FoodCategory fc ON f.category_id = fc.id
            WHERE pf.preorder_id = ?
        ");
        $stmt->bind_param("i", $preorder_id);
        $stmt->execute();
        $foods = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        if (empty($foods)) {
            echo "<p>No hay comidas asociadas a este pedido anticipado.</p>";
        } else {
            foreach ($foods as $food) {
                echo "<p><strong>{$food['name']}</strong> ({$food['category_name']})<br>{$food['description']}</p>";
            }
        }
    }
?>