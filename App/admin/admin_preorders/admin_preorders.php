<?php
require_once '../../db_config.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

$result = $conn->query("SELECT preorder.id, customer.username, reservation.id AS reservation_id, preorder.status FROM preorder JOIN customer ON preorder.customer_id = customer.id JOIN reservation ON preorder.reservation_id = reservation.id");

?>

<h1>Gestión de Pedidos Anticipados</h1>
<table>
    <tr>
        <th>ID</th>
        <th>Cliente</th>
        <th>ID de Reserva</th>
        <th>Estado</th>
        <th>Acciones</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['username'] ?></td>
            <td><?= $row['reservation_id'] ?></td>
            <td><?= $row['status'] ?></td>
            <td>
                <a href="edit_preorder.php?id=<?= $row['id'] ?>">Editar</a> |
                <a href="delete_preorder.php?id=<?= $row['id'] ?>">Eliminar</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
