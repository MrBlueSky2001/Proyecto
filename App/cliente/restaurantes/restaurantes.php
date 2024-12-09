<?php
    // require_once '../../db_config.php';
    // session_start();

    // $stmt = $conn->query("SELECT * FROM Restaurant");
    // $restaurants = $stmt->fetch_all(MYSQLI_ASSOC);

    require_once '../../db_config.php';
    session_start();

    // Inicializar la variable de búsqueda
    $searchQuery = '';

    // Verificar si se ha enviado una consulta de búsqueda
    if (isset($_GET['search'])) {
        $searchQuery = $_GET['search'];
    }

    // Preparar la consulta SQL
    $stmt = $conn->prepare("SELECT * FROM Restaurant WHERE LOWER(name) LIKE LOWER(?)");
    $searchParam = '%' . $searchQuery . '%';
    $stmt->bind_param("s", $searchParam);
    $stmt->execute();
    $restaurants = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Restaurantes</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
            .restaurant-card {
                margin-bottom: 20px;
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
            .btn-custom {
                background-color: #D4AF37; /* Color dorado para los botones */
                color: white;
            }
            .btn-custom:hover {
                background-color: #CDAA31; /* Color dorado más oscuro al hacer hover */
            }
        </style>
    </head>
    <body>
        <h2>Lista de Restaurantes</h2>
        <div class="container">
            <!-- Formulario de búsqueda -->
            <div class="mb-4">
                <form method="GET" action="">
                    <input type="text" name="search" class="form-control" placeholder="Buscar por nombre" value="<?php echo htmlspecialchars($searchQuery); ?>">
                </form>
            </div>
            <div class="row">
                <?php if (count($restaurants) > 0): ?>
                    <?php foreach ($restaurants as $restaurant): ?>
                        <div class="col-md-4">
                            <div class="card restaurant-card">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($restaurant['name']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars($restaurant['description']); ?></p>
                                    <button class="btn btn-custom" onclick="abrirModalReserva(<?php echo $restaurant['id']; ?>)">Hacer Reserva</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <p class="text-center">No se encontraron restaurantes.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Modal de Reserva -->
        <div class="modal fade" id="modalReserva" tabindex="-1" role="dialog" aria-labelledby="modalReservaLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalReservaLabel">Hacer Reserva</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formularioReserva">
                            <input type="hidden" id="idRestaurante" name="restaurant_id">
                            <div class="form-group">
                                <label for="fechaReserva">Fecha</label>
                                <input type="date" class="form-control" id="fechaReserva" name="reservation_date" required>
                            </div>
                            <div class="form-group">
                                <label for="horaReserva">Hora</label>
                                <input type="time" class="form-control" id="horaReserva" name="reservation_time" required>
                            </div>
                            <div class="form-group">
                                <label for="numeroComensales">Número de Comensales</label>
                                <input type="number" class="form-control" id="numeroComensales" name="number_of_guests" min="1" required>
                            </div>
                            <button type="submit" class="btn btn-custom">Confirmar Reserva</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function abrirModalReserva(idRestaurante) {
                document.getElementById('idRestaurante').value = idRestaurante;

                fetch(`ob_hora_restaurante.php?restaurant_id=${idRestaurante}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error al obtener los horarios del restaurante.');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.error) {
                            mostrarModalMensaje('Error', data.error);
                        } else {
                            document.getElementById('horaReserva').setAttribute('min', data.open_time);
                            document.getElementById('horaReserva').setAttribute('max', data.close_time);
                            $('#modalReserva').modal('show');
                        }
                    })
                    .catch(error => {
                        mostrarModalMensaje('Error', error.message);
                    });
            }

            document.getElementById('formularioReserva').addEventListener('submit', function (event) {
                event.preventDefault();

                const datosFormulario = new FormData(this);

                fetch('hacer_reserva.php', {
                    method: 'POST',
                    body: datosFormulario,
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error al realizar la reserva.');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            mostrarModalMensaje('Éxito', 'Reserva realizada con éxito. Si desea realizar un pedido anticipado, vaya al menú y acceda desde ahí.');
                            $('#modalReserva').modal('hide');
                        } else {
                            mostrarModalMensaje('Error', 'Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        mostrarModalMensaje('Error', error.message);
                    });
            });

            function mostrarModalMensaje(titulo, mensaje) {
                document.getElementById('modalMensajeTitulo').textContent = titulo;
                document.getElementById('modalMensajeCuerpo').textContent = mensaje;
                $('#modalMensaje').modal('show');
            }
        </script>

        <!-- Modal de Mensajes -->
        <div class="modal fade" id="modalMensaje" tabindex="-1" role="dialog" aria-labelledby="modalMensajeLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalMensajeTitulo"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="modalMensajeCuerpo"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

    </body>
</html>