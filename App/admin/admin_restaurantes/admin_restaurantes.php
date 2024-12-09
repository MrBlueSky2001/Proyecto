<?php
    require_once '../../db_config.php';
    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        header('Location: ../../login.php');
        exit();
    }

    // Inicializar la variable de búsqueda
    $searchQuery = '';

    // Verificar si se ha enviado una consulta de búsqueda
    if (isset($_GET['search'])) {
        $searchQuery = $_GET['search'];
    }

    // Preparar la consulta SQL
    $stmt = $conn->prepare("SELECT * FROM restaurant WHERE LOWER(name) LIKE LOWER(?)");
    $searchParam = '%' . $searchQuery . '%';
    $stmt->bind_param("s", $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestión de Restaurantes</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
        <style>
            body {
                background-color: #F8F5F2; /* Fondo de la página */
            }
            h1 {
                color: #3498db; /* Título en color azul */
                text-align: center;
                margin-top: 20px;
            }
            .container {
                margin-top: 20px;
            }
            .card {
                border: 1px solid #3498db; /* Borde de la tarjeta en azul */
                border-radius: 8px; /* Bordes redondeados */
            }
            .card-title {
                color: #3498db; /* Título de la tarjeta en azul */
            }
            .card-actions {
                display: flex;
                justify-content: flex-start;
            }
            .edit-btn, .delete-btn {
                border-radius: 0.25rem; /* Bordes redondeados para botones */
            }
            .edit-btn {
                background-color: #FFC107; /* Color amarillo para editar */
                color: white; /* Texto blanco */
            }
            .delete-btn {
                background-color: #DC3545; /* Color rojo para eliminar */
                color: white; /* Texto blanco */
            }
            .modal-header {
                background-color: #3498db; /* Encabezado del modal en azul */
                color: white; /* Texto blanco en el encabezado del modal */
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Gestión de Restaurantes</h1>
            
            <!-- Barra de búsqueda -->
            <form method="GET" class="mb-4">
                <input type="text" name="search" class="form-control" placeholder="Buscar por nombre" value="<?php echo htmlspecialchars($searchQuery); ?>">
            </form>

            <!-- Botón de añadir restaurante -->
            <button class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#anadirRestauranteModal">Añadir Restaurante</button>

            <div class="row mt-4">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
                                <p class="card-text"><strong>Dirección:</strong> <?= htmlspecialchars($row['address']) ?></p>
                                <p class="card-text"><strong>Teléfono:</strong> <?= htmlspecialchars($row['phone_number']) ?></p>
                                <div class="card-actions">
                                    <a href="#" class="btn edit-btn" data-bs-toggle="modal" data-bs-target="#editarRestauranteModal" 
                                        data-id="<?= $row['id'] ?>" 
                                        data-name="<?= htmlspecialchars($row['name']) ?>" 
                                        data-address="<?= htmlspecialchars($row['address']) ?>" 
                                        data-phone="<?= htmlspecialchars($row['phone_number']) ?>" 
                                        data-email="<?= htmlspecialchars($row['email']) ?>" 
                                        data-description="<?= htmlspecialchars($row['description']) ?>" 
                                        data-open-time="<?= htmlspecialchars($row['open_time']) ?>" 
                                        data-close-time="<?= htmlspecialchars($row['close_time']) ?>">Editar</a>
                                    <a href="#" class="btn delete-btn" data-bs-toggle="modal" data-bs-target="#eliminarRestauranteModal" data-id="<?= $row['id'] ?>">Eliminar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Modal para añadir nuevo restaurante -->
        <div class="modal fade" id="anadirRestauranteModal" tabindex="-1" aria-labelledby="anadirRestauranteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="anadirRestauranteModalLabel">Añadir Restaurante</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="anadirRestauranteForm">
                            <div class="mb-3">
                                <label for="restaurantName" class="form-label">Nombre del Restaurante</label>
                                <input type="text" class="form-control" id="restaurantName" required>
                            </div>
                            <div class="mb-3">
                                <label for="restaurantAddress" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="restaurantAddress" required>
                            </div>
                            <div class="mb-3">
                                <label for="restaurantPhone" class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="restaurantPhone" required>
                            </div>
                            <div class="mb-3">
                                <label for="restaurantEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="restaurantEmail" required>
                            </div>
                            <div class="mb-3">
                                <label for="restaurantDescription" class="form-label">Descripción</label>
                                <textarea class="form-control" id="restaurantDescription" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="restaurantOpenTime" class="form-label">Hora de Apertura</label>
                                <input type="time" class="form-control" id="restaurantOpenTime" required>
                            </div>
                            <div class="mb-3">
                                <label for="restaurantCloseTime" class="form-label">Hora de Cierre</label>
                                <input type="time" class="form-control" id="restaurantCloseTime" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Guardar Restaurante</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para editar restaurante -->
        <div class="modal fade" id="editarRestauranteModal" tabindex="-1" aria-labelledby="editarRestauranteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarRestauranteModalLabel">Editar Restaurante</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editarRestauranteForm">
                            <input type="hidden" id="editarRestauranteId">
                            <div class="mb-3">
                                <label for="editarRestauranteName" class="form-label">Nombre del Restaurante</label>
                                <input type="text" class="form-control" id="editarRestauranteName" required>
                            </div>
                            <div class="mb-3">
                                <label for="editarRestauranteAddress" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="editarRestauranteAddress" required>
                            </div>
                            <div class="mb-3">
                                <label for="editarRestaurantePhone" class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="editarRestaurantePhone" required>
                            </div>
                            <div class="mb-3">
                                <label for="editarRestauranteEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="editarRestauranteEmail" required>
                            </div>
                            <div class="mb-3">
                                <label for="editarRestauranteDescription" class="form-label">Descripción</label>
                                <textarea class="form-control" id="editarRestauranteDescription" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="editarRestauranteOpenTime" class="form-label">Hora de Apertura</label>
                                <input type="time" class="form-control" id="editarRestauranteOpenTime" required>
                            </div>
                            <div class="mb-3">
                                <label for="editarRestauranteCloseTime" class="form-label">Hora de Cierre</label>
                                <input type="time" class="form-control" id="editarRestauranteCloseTime" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para eliminar restaurante -->
        <div class="modal fade" id="eliminarRestauranteModal" tabindex="-1" aria-labelledby="eliminarRestauranteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="eliminarRestauranteModalLabel">Eliminar Restaurante</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Estás seguro de que deseas eliminar este restaurante?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <a href="#" id="eliminarRestauranteButton" class="btn btn-danger">Eliminar</a>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Añadir nuevo restaurante
            document.getElementById('anadirRestauranteForm').addEventListener('submit', function(e) {
                e.preventDefault();

                var name = document.getElementById('restaurantName').value;
                var address = document.getElementById('restaurantAddress').value;
                var phone = document.getElementById('restaurantPhone').value;
                var email = document.getElementById('restaurantEmail').value;
                var description = document.getElementById('restaurantDescription').value;
                var openTime = document.getElementById('restaurantOpenTime').value;
                var closeTime = document.getElementById('restaurantCloseTime').value;

                fetch('anadir_restaurante.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `name=${encodeURIComponent(name)}&address=${encodeURIComponent(address)}&phone_number=${encodeURIComponent(phone)}&email=${encodeURIComponent(email)}&description=${encodeURIComponent(description)}&open_time=${encodeURIComponent(openTime)}&close_time=${encodeURIComponent(closeTime)}`
                }).then(response => {
                    return response.json(); // Aquí se espera un JSON
                }).then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error al añadir el restaurante: ' + data.error);
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    alert('Se produjo un error al procesar la solicitud.');
                });
            });

            // Editar restaurante
            var editarRestauranteModal = document.getElementById('editarRestauranteModal');
            editarRestauranteModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget; // Botón que abrió el modal
                var id = button.getAttribute('data-id');
                var name = button.getAttribute('data-name');
                var address = button.getAttribute('data-address');
                var phone = button.getAttribute('data-phone');
                var email = button.getAttribute('data-email');
                var description = button.getAttribute('data-description'); 
                var openTime = button.getAttribute('data-open-time'); 
                var closeTime = button.getAttribute('data-close-time'); 

                // Establece los valores en los campos del formulario
                document.getElementById('editarRestauranteId').value = id;
                document.getElementById('editarRestauranteName').value = name;
                document.getElementById('editarRestauranteAddress').value = address;
                document.getElementById('editarRestaurantePhone').value = phone;
                document.getElementById('editarRestauranteEmail').value = email; 
                document.getElementById('editarRestauranteDescription').value = description; 
                document.getElementById('editarRestauranteOpenTime').value = openTime;
                document.getElementById('editarRestauranteCloseTime').value = closeTime;
            });

            document.getElementById('editarRestauranteForm').addEventListener('submit', function (e) {
                e.preventDefault();
                var id = document.getElementById('editarRestauranteId').value;
                var name = document.getElementById('editarRestauranteName').value;
                var address = document.getElementById('editarRestauranteAddress').value;
                var phone = document.getElementById('editarRestaurantePhone').value;
                var email = document.getElementById('editarRestauranteEmail').value;
                var description = document.getElementById('editarRestauranteDescription').value;
                var openTime = document.getElementById('editarRestauranteOpenTime').value;
                var closeTime = document.getElementById('editarRestauranteCloseTime').value;

                fetch('edit_restaurante.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${id}&name=${name}&address=${address}&phone_number=${phone}&email=${email}&description=${description}&open_time=${encodeURIComponent(openTime)}&close_time=${encodeURIComponent(closeTime)}`
                }).then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                }).then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error al editar el restaurante: ' + (data.error || 'Error desconocido'));
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    alert('Se produjo un error al procesar la solicitud.');
                });
            });

            // Eliminar restaurante
            var eliminarRestauranteModal = document.getElementById('eliminarRestauranteModal');
            eliminarRestauranteModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var id = button.getAttribute('data-id');
                var deleteButton = document.getElementById('eliminarRestauranteButton');
                deleteButton.href = 'eliminar_restaurante.php?id=' + id;
            });
        </script>
    </body>
</html>