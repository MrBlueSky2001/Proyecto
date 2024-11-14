<?php
session_start();
require_once '../../db_config.php';

$user_id = $_SESSION['user']['id'];
$stmt = $conn->prepare("SELECT * FROM Reservation WHERE customer_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$reservations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Reservas</title>
</head>
<body>
    <h2>Mis Reservas</h2>
    <ul>
        <?php foreach ($reservations as $reservation): ?>
            <li>
                <p>Reserva para el <?php echo htmlspecialchars($reservation['reservation_date']); ?> a las <?php echo htmlspecialchars($reservation['reservation_time']); ?></p>
                <a href="cancel_reservation.php?id=<?php echo $reservation['id']; ?>">Cancelar</a>
                <a href="edit_reservation.php?id=<?php echo $reservation['id']; ?>">Modificar</a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
