<?php
require_once '../../db_config.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

$result = $conn->query("SELECT reservation.id, customer.username, restaurant.name AS restaurant_name, reservation.reservation_date, reservation.reservation_time FROM reservation JOIN customer ON reservation.customer_id = customer.id JOIN restaurant ON reservation.restaurant_id = restaurant.id");

?>

<h1>Gestión de Reservas</h1>
<table>
    <tr>
        <th>ID</th>
        <th>Cliente</th>
        <th>Restaurante</th>
        <th>Fecha</th>
        <th>Hora</th>
        <th>Acciones</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['username'] ?></td>
            <td><?= $row['restaurant_name'] ?></td>
            <td><?= $row['reservation_date'] ?></td>
            <td><?= $row['reservation_time'] ?></td>
            <td>
                <a href="edit_reservation.php?id=<?= $row['id'] ?>">Editar</a> |
                <a href="delete_reservation.php?id=<?= $row['id'] ?>">Eliminar</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
