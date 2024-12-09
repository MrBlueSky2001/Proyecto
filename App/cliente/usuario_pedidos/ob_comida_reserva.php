<?php
    require_once '../../db_config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $reservation_id = $_POST['reservation_id'];

        // Obtener el ID del restaurante asociado a la reserva
        $stmt = $conn->prepare("SELECT restaurant_id FROM Reservation WHERE id = ?");
        $stmt->bind_param("i", $reservation_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $reservation = $result->fetch_assoc();

        if (!$reservation) {
            echo "<p>Error: Reserva no encontrada.</p>";
            exit;
        }

        $restaurant_id = $reservation['restaurant_id'];

        // Obtener comidas del restaurante con sus categorías
        $stmt = $conn->prepare("
            SELECT f.id, f.name, f.price, fc.category_name 
            FROM Food f
            JOIN FoodCategory fc ON f.category_id = fc.id
            WHERE f.restaurant_id = ?
        ");
        $stmt->bind_param("i", $restaurant_id);
        $stmt->execute();
        $foods = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        if (empty($foods)) {
            echo "<p>No hay comidas disponibles para este restaurante.</p>";
        } else {
            $current_category = null;
            foreach ($foods as $food) {
                if ($current_category !== $food['category_name']) {
                    if ($current_category !== null) {
                        echo "</div>";
                    }
                    $current_category = $food['category_name'];
                    echo "<div><h5>{$current_category}</h5>";
                }
                echo "<div>
                        <input type='checkbox' name='food' value='{$food['id']}' id='food_{$food['id']}'>
                        <label for='food_{$food['id']}'>{$food['name']} - {$food['price']}€</label>
                    </div>";
            }
            echo "</div>";
        }
    }
?>
