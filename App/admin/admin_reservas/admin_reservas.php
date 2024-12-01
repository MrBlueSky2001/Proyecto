<?php
require_once '../../db_config.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

// Consulta para obtener datos de la reserva
$result = $conn->query("
    SELECT reservation.id, customer.username, restaurant.name AS restaurant_name,
           reservation.reservation_date, reservation.reservation_time, reservation.table_number,
           reservation.number_of_guests, reservation.status
    FROM reservation
    JOIN customer ON reservation.customer_id = customer.id
    JOIN restaurant ON reservation.restaurant_id = restaurant.id
");
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Gestión de Reservas</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
        <script
            type="module"
            src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"
        ></script>
        <script
            nomodule
            src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"
        ></script>
        <style>
            body {
                background-color: #F8F5F2; /* Fondo de la página */
            }
            h1 {
                color: #3498db; /* Título en color azul */
                text-align: center;
                margin-top: 20px;
            }
            .table {
                margin: 20px auto;
                background-color: white; /* Fondo blanco para la tabla */
                border-radius: 8px; /* Bordes redondeados */
                overflow: hidden; /* Para que los bordes redondeados se apliquen */
            }
            .table th {
                background-color: #3498db; /* Encabezado de la tabla en azul */
                color: white; /* Texto blanco en el encabezado */
            }
            .status-button {
                border: none;
                padding: 5px 10px;
                border-radius: 5px;
                color: white;
            }
        </style>
    </head>
    <body>
        <h1>Gestión de Reservas</h1>
        <div class="container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Restaurante</th>
                        <th>Fecha de la reserva</th>
                        <th>Hora de la reserva</th>
                        <th>Mesa</th>
                        <th>Número de comensales</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['restaurant_name']) ?></td>
                            <td><?= htmlspecialchars($row['reservation_date']) ?></td>
                            <td><?= htmlspecialchars($row['reservation_time']) ?></td>
                            <td><?= htmlspecialchars($row['table_number']) ?></td>
                            <td><?= htmlspecialchars($row['number_of_guests']) ?></td>
                            <td>
                                <div id="status-container-<?= $row['id'] ?>">
                                    <?php
                                    // Configurar colores según el estado
                                    $status = $row['status'];
                                    $color = $status === 'pendiente' ? 'blue' : ($status === 'confirmado' ? 'green' : 'red');
                                    ?>
                                    <button 
                                        class="status-button"
                                        style="background-color: <?= $color ?>;"
                                        onclick="editarEstatus(<?= $row['id'] ?>, '<?= $status ?>')">
                                        <?= ucfirst($status) ?>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <script>
            function editarEstatus(reservationId, currentStatus) {
                const container = document.getElementById(`status-container-${reservationId}`);
                container.innerHTML = `
                    <select onchange="actualizarEstatus(${reservationId}, this.value)">
                        <option value="pendiente" ${currentStatus === 'pendiente' ? 'selected' : ''}>Pendiente</option>
                        <option value="confirmado" ${currentStatus === 'confirmado' ? 'selected' : ''}>Confirmado</option>
                        <option value="cancelado" ${currentStatus === 'cancelado' ? 'selected' : ''}>Cancelado</option>
                    </select>
                `;
            }

            function actualizarEstatus(reservationId, newStatus) {
                const formData = new FormData();
                formData.append('id', reservationId);
                formData.append('status', newStatus);

                fetch('act_estatus.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'success') {
                        const container = document.getElementById(`status-container-${reservationId}`);
                        const color = newStatus === 'pendiente' ? 'blue' : (newStatus === 'confirmado' ? 'green' : 'red');
                        container.innerHTML = `
                            <button 
                                style="background-color: ${color}; color: white; border: none; padding: 5px 10px; border-radius: 5px;"
                                onclick="editStatus(${reservationId}, '${newStatus}')">
                                ${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)}
                            </button>
                        `;
                    } else {
                        alert('Error al actualizar el estado.');
                    }
                });
            }
        </script>
    </body>
</html>