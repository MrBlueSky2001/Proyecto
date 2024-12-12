<?php
    require_once '../../db_config.php';

    // Iniciamos la sesión para tener acceso a las variables de sesión, como el usuario logueado
    session_start();

    // Verificamos que el usuario esté autenticado y tenga el rol de 'admin'. Si no es así, redirigimos al login.
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        // Si el usuario no es admin, redirigimos al login
        header('Location: ../../login.php');
        exit(); // Terminamos el script si no se cumplen las condiciones
    }

    // Realizamos una consulta SQL para obtener la información de los pedidos anticipados y las reservas asociadas
    $result = $conn->query("
        SELECT preorder.id AS preorder_id, customer.username, reservation.id AS reservation_id, 
            reservation.reservation_date, reservation.reservation_time, preorder.status 
        FROM preorder
        JOIN customer ON preorder.customer_id = customer.id
        JOIN reservation ON preorder.reservation_id = reservation.id
        ORDER BY reservation.reservation_date DESC, reservation.reservation_time DESC
    ");
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestión de Pedidos Anticipados</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <style>
            body {
                background-color: #F8F5F2; /* Fondo de la página */
            }
            h1 {
                color: #3498db; /* Título en color azul */
                text-align: center;
                margin-top: 20px;
            }
            .card {
                border: 1px solid #3498db; /* Borde de la tarjeta en azul */
                border-radius: 8px; /* Bordes redondeados */
                margin-bottom: 15px; /* Espacio entre tarjetas */
            }
            .card-header {
                background-color: #3498db; /* Fondo del encabezado de la tarjeta */
                color: white; /* Texto blanco en el encabezado */
            }
            .status-button {
                border: none;
                padding: 5px 10px;
                border-radius: 5px;
                color: white;
            }
            .status-pending {
                background-color: blue;
            }
            .status-confirmed {
                background-color: green;
            }
            .status-canceled {
                background-color: red;
            }
        </style>
    </head>
    <body>
        <div class="container mt-5">
            <h1 class="mb-4">Gestión de Pedidos Anticipados</h1>
            <?php if ($result->num_rows > 0): ?>
                <div class="accordion" id="pedidoAccordion">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="card">
                            <div class="card-header" id="heading-<?= $row['reservation_id'] ?>">
                                <h5 class="mb-0">
                                    <button class="btn btn-link text-white" type="button" data-toggle="collapse" data-target="#collapse-<?= $row['reservation_id'] ?>" aria-expanded="true" aria-controls="collapse-<?= $row['reservation_id'] ?>">
                                        Reserva ID <?= $row['reservation_id'] ?> - Fecha: <?= $row['reservation_date'] ?> - Hora: <?= $row['reservation_time'] ?>
                                    </button>
                                </h5>
                            </div>
                            <div id="collapse-<?= $row['reservation_id'] ?>" class="collapse" aria-labelledby="heading-<?= $row['reservation_id'] ?>" data-parent="#pedidoAccordion">
                                <div class="card-body">
                                    <p><strong>Cliente:</strong> <?= htmlspecialchars($row['username']) ?></p>
                                    <p><strong>ID Pedido Anticipado:</strong> 
                                        <a href="#" class="preorder-link" data-preorder-id="<?= $row['preorder_id'] ?>">
                                            <?= $row['preorder_id'] ?>
                                        </a>
                                    </p>
                                    <div id="status-container-<?= $row['preorder_id'] ?>">
                                        <?php
                                        $status = $row['status'];
                                        $statusClass = $status === 'pendiente' ? 'status-pending' : ($status === 'confirmado' ? 'status-confirmed' : 'status-canceled');
                                        ?>
                                        <button 
                                            class="status-button <?= $statusClass ?>"
                                            onclick="editarEstatus(<?= $row['preorder_id'] ?>, '<?= $status ?>')">
                                            <?= ucfirst($status) ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>No hay pedidos anticipados disponibles.</p>
            <?php endif; ?>
        </div>

        <!-- Modal para mostrar las comidas -->
        <div class="modal fade" id="ComidaModal" tabindex="-1" aria-labelledby="ComidaModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ComidaModalLabel">Comidas del Pedido Anticipado</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="modal-body-content">
                        <!-- Aquí se cargará el contenido dinámicamente -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Mostramos el modal con las comidas del pedido anticipado
            $(document).on('click', '.preorder-link', function(e) {
                e.preventDefault();
                var preorderId = $(this).data('preorder-id'); // Obtenemos el ID del pedido anticipado
                $('#modal-body-content').html('<p>Cargando...</p>'); // Mostramos mensaje de carga
                $('#ComidaModal').modal('show'); // Mostrar el modal

                // Realizamos una solicitud AJAX para obtener las comidas del pedido anticipado
                $.ajax({
                    url: 'ob_comida_pedido.php',
                    type: 'POST',
                    data: { preorder_id: preorderId },
                    success: function(data) {
                        $('#modal-body-content').html(data); // Cargamos el contenido de las comidas en el modal
                    },
                    error: function() {
                        $('#modal-body-content').html('<p>Error al cargar las comidas.</p>'); // Mostramos error si falla la solicitud
                    }
                });
            });

            // Cambiamos el estado del pedido anticipado
            function editarEstatus(preorderId, currentStatus) {
                const container = document.getElementById(`status-container-${preorderId}`);
                container.innerHTML = `
                    <select onchange="actualizarEstatus(${preorderId}, this.value)">
                        <option value="pendiente" ${currentStatus === 'pendiente' ? 'selected' : ''}>Pendiente</option>
                        <option value="confirmado" ${currentStatus === 'confirmado' ? 'selected' : ''}>Confirmado</option>
                        <option value="cancelado" ${currentStatus === 'cancelado' ? 'selected' : ''}>Cancelado</option>
                    </select>
                `;
            }

            // Actualizamos el estado del pedido anticipado en la base de datos
            function actualizarEstatus(preorderId, newStatus) {
                const formData = new FormData();
                formData.append('id', preorderId); // Agregamos el ID del pedido anticipado
                formData.append('status', newStatus); // Agregamos el nuevo estado

                // Enviamos la solicitud AJAX para actualizar el estado
                fetch('act_estatus_pedido.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'success') {
                        const container = document.getElementById(`status-container-${preorderId}`);
                        const statusClass = newStatus === 'pendiente' ? 'status-pending' : (newStatus === 'confirmado' ? 'status-confirmed' : 'status-canceled');
                        // Actualizamos el botón con el nuevo estado
                        container.innerHTML = `
                            <button 
                                class="status-button ${statusClass}"
                                onclick="editarEstatus(${preorderId}, '${newStatus}')">
                                ${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)}
                            </button>
                        `;
                    } else {
                        alert('Error al actualizar el estado.'); // Mostramos alerta si hay un error
                    }
                });
            }
        </script>
    </body>
</html>