<?php
    require_once '../../db_config.php';

    // Verificamos que la solicitud sea de tipo POST (se espera que se envíen datos mediante POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtenemos el ID del pedido anticipado (preorder_id) enviado a través de POST
        $preorder_id = $_POST['preorder_id'];

        // Preparamos una consulta SQL para obtener las comidas asociadas con el pedido anticipado
        // Se hace un JOIN entre las tablas PreOrder_Food (relaciona el pedido con la comida),
        // Food (información de las comidas) y FoodCategory (categorías de las comidas)
        $stmt = $conn->prepare("
            SELECT f.name, f.description, fc.category_name 
            FROM PreOrder_Food pf
            JOIN Food f ON pf.food_id = f.id
            JOIN FoodCategory fc ON f.category_id = fc.id
            WHERE pf.preorder_id = ?
        ");
        
        // Vinculamos el parámetro (preorder_id) a la consulta de forma segura (usando bind_param)
        // El tipo "i" indica que el parámetro es un número entero
        $stmt->bind_param("i", $preorder_id);
        
        // Ejecutamos la consulta
        $stmt->execute();
        
        // Obtenemos los resultados de la consulta y convertirlos en un array asociativo
        $foods = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Verificamos si el resultado está vacío (es decir, si no hay comidas asociadas al pedido anticipado)
        if (empty($foods)) {
            // Si no se encuentran comidas, mostramos un mensaje informando al usuario
            echo "<p>No hay comidas asociadas a este pedido anticipado.</p>";
        } else {
            // Si hay comidas, iteramos sobre ellas y las mostramos una por una
            foreach ($foods as $food) {
                // Mostramos el nombre, categoría y descripción de cada comida
                echo "<p><strong>{$food['name']}</strong> ({$food['category_name']})<br>{$food['description']}</p>";
            }
        }
    }
?>