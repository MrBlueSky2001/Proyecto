<?php
    require_once '../../db_config.php';

    // Iniciamos sesión para asegurar que solo los usuarios autenticados puedan acceder
    session_start();

    // Verificamos si el usuario está autenticado y si tiene el rol de 'admin'
    // Si no está autenticado o no tiene el rol adecuado, redirigimos a la página de login
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        header('Location: ../../login.php');  // Redirigimos al login si no es admin
        exit();
    }

    // Realizamos la consulta SQL para obtener los datos de las reservas
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
                background-color: #F8F5F2;
            }
            h1 {
                color: #3498db;
                text-align: center;
                margin-top: 20px;
            }
            .table {
                margin: 20px auto;
                background-color: white;
                border-radius: 8px;
                overflow: hidden;
            }
            .table th {
                background-color: #3498db;
                color: white;
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
            // Función para permitir la edición del estado de la reserva
            function editarEstatus(reservationId, currentStatus) {
                const container = document.getElementById(`status-container-${reservationId}`);
                // Generamos un menú desplegable para cambiar el estado de la reserva
                container.innerHTML = `
                    <select onchange="actualizarEstatus(${reservationId}, this.value)">
                        <option value="pendiente" ${currentStatus === 'pendiente' ? 'selected' : ''}>Pendiente</option>
                        <option value="confirmado" ${currentStatus === 'confirmado' ? 'selected' : ''}>Confirmado</option>
                        <option value="cancelado" ${currentStatus === 'cancelado' ? 'selected' : ''}>Cancelado</option>
                    </select>
                `;
            }

            // Función para actualizar el estado de la reserva en la base de datos
            function actualizarEstatus(reservationId, newStatus) {
                const formData = new FormData();
                formData.append('id', reservationId);  // Agregamos el ID de la reserva
                formData.append('status', newStatus);  // Agregamos el nuevo estado seleccionado

                // Enviamos la solicitud POST al servidor para actualizar el estado usando fetch, el cual es una API moderna de JavaScript
                // que realiza peticiones HTTP de manera asíncrona.
                fetch('act_estatus.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())  // Obtenemos la respuesta del servidor
                .then(data => {
                    console.log('Respuesta del servidor:', data);
                    if (data === 'success') {
                        // Si la actualización tuvo éxito, actualizamos la interfaz con el nuevo estado
                        const container = document.getElementById(`status-container-${reservationId}`);
                        const color = newStatus === 'pendiente' ? 'blue' : (newStatus === 'confirmado' ? 'green' : 'red');
                        container.innerHTML = `
                            <button 
                                style="background-color: ${color}; color: white; border: none; padding: 5px 10px; border-radius: 5px;"
                                onclick="editarEstatus(${reservationId}, '${newStatus}')">
                                ${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)}  <!-- Primer letra en mayúscula -->
                            </button>
                        `;
                    } else {
                        // Si ocurrió un error, mostramos un mensaje de alerta
                        alert('Error al actualizar el estado.');
                    }
                });
            }
        </script>
    </body>
</html>