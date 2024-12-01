<?php
    session_start();
    require_once '../../db_config.php';

    // Verificar que el usuario está autenticado
    if (!isset($_SESSION['user']['id'])) {
        echo "Error: Usuario no autenticado.";
        exit;
    }

    $user_id = $_SESSION['user']['id'];

    // Obtener reservas del usuario
    $stmt = $conn->prepare("
        SELECT r.*, res.name AS restaurant_name 
        FROM Reservation r 
        JOIN Restaurant res ON r.restaurant_id = res.id 
        WHERE r.customer_id = ? 
        ORDER BY r.reservation_date DESC, r.reservation_time DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $reservations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Pedidos Anticipados</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <style>
            body {
                background-color: #F8F5F2; /* Fondo de la página */
            }
            h2 {
                text-align: center;
                margin-top: 20px;
                color: #333; /* Color del título */
            }
            .list-group-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .btn-custom {
                background-color: #D4AF37; /* Color dorado para los botones */
                color: white;
            }
            .btn-custom:hover {
                background-color: #CDAA31; /* Color dorado más oscuro al hacer hover */
            }
            .modal-header {
                background-color: #343a40; /* Color de fondo del encabezado del modal */
                color: white; /* Color del texto del encabezado del modal */
            }
            .modal-footer {
                justify-content: space-between; /* Espacio entre botones del modal */
            }
            .footer {
                background-color: #000; /* Fondo negro para el footer */
                color: #ccc;
                text-align: center;
                padding: 15px;
                position: fixed;
                bottom: 0;
                width: 100%;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h2>Mis Reservas</h2>
            <ul class="list-group">
                <?php foreach ($reservations as $reservation): ?>
                    <li class="list-group-item">
                        <p>Reserva en <?php echo htmlspecialchars($reservation['restaurant_name']); ?> - Fecha: <?php echo htmlspecialchars($reservation['reservation_date']); ?> - Hora: <?php echo htmlspecialchars($reservation['reservation_time']); ?></p>
                        <button class="btn btn-custom" 
                                data-toggle="modal" 
                                data-target="#pedidoModal" 
                                data-reservation-id="<?php echo $reservation['id']; ?>">
                            Realizar Pedido Anticipado
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="pedidoModal" tabindex="-1" role="dialog" aria-labelledby="pedidoModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="pedidoModalLabel">Selecciona tus Comidas</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="modal-body-content">
                        <!-- Aquí se cargarán las comidas -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-custom" id="confirm-preorder">Confirmar Pedido</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                // Modal genérico para mensajes
                function showAlertModal(title, message, type) {
                    var modalHeader = $('#alertModal .modal-header');
                    var modalTitle = $('#alertModal .modal-title');
                    var modalBody = $('#alertModal .modal-body');

                    // Cambiar estilo del encabezado según el tipo
                    modalHeader.removeClass('bg-success bg-danger').addClass(type === 'success' ? 'bg-success' : 'bg-danger');
                    modalTitle.text(title);
                    modalBody.text(message);

                    // Mostrar el modal
                    $('#alertModal').modal('show');
                }

                $('#pedidoModal').on('show.bs.modal', function(event) {
                    var button = $(event.relatedTarget); // Botón que abrió el modal
                    var reservationId = button.data('reservation-id'); // Extraer información de los atributos data-*

                    // Asignar el reservation_id al modal
                    $(this).data('reservation-id', reservationId);

                    // Cargar las comidas asociadas al restaurante de la reserva
                    $.post('ob_comida_reserva.php', { reservation_id: reservationId }, function(data) {
                        $('#modal-body-content').html(data);
                    });
                });

                $('#confirm-preorder').click(function() {
                    var selectedFoods = [];
                    $('input[name="food"]:checked').each(function() {
                        selectedFoods.push($(this).val());
                    });

                    var reservationId = $('#pedidoModal').data('reservation-id');

                    // Enviar los datos al servidor para guardar el pedido
                    $.post('guardar_pedido.php', { 
                        foods: selectedFoods, 
                        reservation_id: reservationId 
                    })
                    .done(function(response) {
                        showAlertModal('Éxito', 'Pedido anticipado guardado con éxito', 'success');
                        $('#pedidoModal').modal('hide');
                    })
                    .fail(function() {
                        showAlertModal('Error', 'Error al guardar el pedido anticipado', 'danger');
                    });
                });
            });
        </script>

        <!-- Modal genérico para alertas -->
        <div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success">
                        <h5 class="modal-title" id="alertModalLabel">Título</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Mensaje
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>&copy; 2023 Tu Empresa. Todos los derechos reservados.</p>
        </div>
    </body>
</html>