<?php
require_once '../../db_config.php';

$stmt = $conn->query("SELECT * FROM Restaurant");
$restaurants = $stmt->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restaurantes</title>
</head>
<body>
    <h2>Lista de Restaurantes</h2>
    <ul>
        <?php foreach ($restaurants as $restaurant): ?>
            <li>
                <h3><?php echo htmlspecialchars($restaurant['name']); ?></h3>
                <p><?php echo htmlspecialchars($restaurant['description']); ?></p>
                <a href="make_reservation.php?restaurant_id=<?php echo $restaurant['id']; ?>">Hacer Reserva</a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
