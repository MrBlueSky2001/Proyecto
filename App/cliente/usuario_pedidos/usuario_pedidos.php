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
                background-color: #F8F5F2;
            }
            h2 {
                text-align: center;
                margin-top: 20px;
                color: #333;
            }
            .list-group-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .btn-custom {
                background-color: #D4AF37;
                color: white;
            }
            .btn-custom:hover {
                background-color: #CDAA31;
            }
            .modal-header {
                background-color: #343a40;
                color: white;
            }
            .modal-footer {
                justify-content: space-between;
            }
            .footer {
                background-color: #000;
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
                    <div>
                        <h5>Selecciona un Método de Pago</h5>
                        <select id="payment-method" class="form-control">
                            <option value="">Selecciona un método de pago</option>
                            <?php
                            // Obtener métodos de pago del cliente
                            $stmt = $conn->prepare("SELECT id, type FROM PaymentMethod WHERE customer_id = ?");
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $payment_methods = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                            foreach ($payment_methods as $method) {
                                echo "<option value='{$method['id']}'>{$method['type']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-custom" id="confirmar-predido">Confirmar Pedido</button>
                    </div>
                </div>
            </div>
        </div>

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

                $('#confirmar-predido').click(function() {
                    var selectedFoods = [];
                    
                    // Recorre todos los checkboxes de comida seleccionados
                    $('input[name="food"]:checked').each(function() {
                        selectedFoods.push($(this).val());
                    });

                    // Obtener el método de pago seleccionado
                    var paymentMethodId = $('#payment-method').val();

                    // Validar que se haya seleccionado al menos un plato y un método de pago
                    if (selectedFoods.length === 0) {
                        showAlertModal('Error', 'Debes seleccionar al menos un plato para realizar el pedido.', 'danger');
                        return; // No continuar con el envío del pedido
                    }

                    if (!paymentMethodId) {
                        showAlertModal('Error', 'Debes seleccionar un método de pago para continuar.', 'danger');
                        return; // No continuar con el envío del pedido
                    }

                    var reservationId = $('#pedidoModal').data('reservation-id');

                    // Enviar los datos al servidor para guardar el pedido
                    $.post('guardar_pedido.php', { 
                        foods: selectedFoods, 
                        reservation_id: reservationId,
                        payment_method_id: paymentMethodId // Enviar el método de pago
                    })
                    .done(function(response) {
                        showAlertModal('Éxito', 'Pedido anticipado guardado con éxito. Si no asistes a tu reserva se te descontará el precio de los platos seleccionados.', 'success');
                        $('#pedidoModal').modal('hide');
                    })
                    .fail(function() {
                        showAlertModal('Error', 'Error al guardar el pedido anticipado', 'danger');
                    });
                });
            });
        </script>
    </body>
</html>