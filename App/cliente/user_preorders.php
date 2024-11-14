<?php
session_start();
require_once '../../db_config.php';

$user_id = $_SESSION['user']['id'];
$stmt = $conn->prepare("SELECT * FROM PreOrder WHERE customer_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$preorders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedidos Anticipados</title>
</head>
<body>
    <h2>Mis Pedidos Anticipados</h2>
    <ul>
        <?php foreach ($preorders as $preorder): ?>
            <li>
                <p>Pedido para reserva ID <?php echo $preorder['reservation_id']; ?> - Estado: <?php echo $preorder['status']; ?></p>
                <a href="manage_preorder.php?id=<?php echo $preorder['id']; ?>">Gestionar Pedido</a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
