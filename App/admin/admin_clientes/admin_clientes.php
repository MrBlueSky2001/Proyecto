<?php
    require_once '../../db_config.php';
    session_start(); // Iniciamos la sesión para poder verificar si el usuario está logueado

    // Verificamos que el usuario esté autenticado y sea administrador
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        header('Location: ../../login.php'); // Redirigimos al login si no es admin
        exit();
    }

    // Realizamos una consulta SQL para obtener todos los clientes desde la base de datos
    $result = $conn->query("SELECT id, username, email, dni, phone_number, address, role FROM customer");
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Gestión de Clientes</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
        <style>
            body {
                background-color: #F8F5F2;
            }
            .container {
                margin-top: 50px;
            }
            h1 {
                color: #3498db;
                text-align: center;
                margin-bottom: 20px;
            }
            .table {
                background-color: white;
                border-radius: 8px;
                overflow: hidden;
            }
            .table th {
                background-color: #3498db;
                color: white;
            }
            .table td a {
                text-decoration: none; 
            }
            .table td a:hover {
                text-decoration: underline;
            }
            .modal-header {
                background-color: #3498db; 
                color: white; 
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Gestión de Clientes</h1>

            <!-- Tabla de clientes -->
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre de Usuario</th>
                        <th>Email</th>
                        <th>DNI</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['dni']) ?></td>
                            <td><?= htmlspecialchars($row['phone_number']) ?></td>
                            <td><?= htmlspecialchars($row['address']) ?></td>
                            <td><?= htmlspecialchars($row['role']) ?></td>
                            <td>
                                <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#editClienteModal" data-id="<?= $row['id'] ?>" data-username="<?= htmlspecialchars($row['username']) ?>" data-email="<?=htmlspecialchars($row['email']) ?>" data-dni="<?= htmlspecialchars($row['dni']) ?>" data-phone="<?= htmlspecialchars($row['phone_number']) ?>" data-address="<?= htmlspecialchars($row['address']) ?>" data-role="<?= htmlspecialchars($row['role']) ?>">
                                    <ion-icon name="pencil-outline"></ion-icon> Editar
                                </a> |
                                <a href="#" class="text-danger" data-bs-toggle="modal" data-bs-target="#eliminarClienteModal" data-id="<?= $row['id'] ?>">
                                    <ion-icon name="trash-outline"></ion-icon> Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Modal de editar cliente -->
            <div class="modal fade" id="editClienteModal" tabindex="-1" aria-labelledby="editClienteModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editClienteModalLabel">Editar Cliente</h5>
                            <button type=button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editClienteForm">
                                <input type="hidden" id="editCustomerId">
                                <div class="mb-3">
                                    <label for="editNombre" class="form-label">Nombre de Usuario</label>
                                    <input type="text" class="form-control" id="editNombre" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editEmail" class="form-label">Email</label>
                                    <input type="text" class="form-control" id="editEmail" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editTelefono" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" id="editTelefono" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editDireccion" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="editDireccion" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editRole" class="form-label">Rol</label>
                                    <select class="form-control" id="editRole" required>
                                        <option value="admin">Admin</option>
                                        <option value="customer">Customer</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal de confirmación de eliminación -->
            <div class="modal fade" id="eliminarClienteModal" tabindex="-1" aria-labelledby="eliminarClienteModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="eliminarClienteModalLabel">Eliminar Cliente</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            ¿Estás seguro de que deseas eliminar este cliente?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <a href="#" id="botonConfirmarEliminacion" class="btn btn-danger">Eliminar</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <script>
           // Rellenamos el formulario del modal de edición con los datos del cliente seleccionado
           var editClienteModal = document.getElementById('editClienteModal');
            editClienteModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget; // Obtenemos el botón que activó el modal
                var id = button.getAttribute('data-id');
                var username = button.getAttribute('data-username');
                var email = button.getAttribute('data-email');
                var phone = button.getAttribute('data-phone');
                var address = button.getAttribute('data-address');
                var role = button.getAttribute('data-role');

                document.getElementById('editCustomerId').value = id;
                document.getElementById('editNombre').value = username;
                document.getElementById('editEmail').value = email;
                document.getElementById('editTelefono').value = phone;
                document.getElementById('editDireccion').value = address;
                document.getElementById('editRole').value = role; 
            });

            // Manejamos el envío del formulario de edición
            document.getElementById('editClienteForm').addEventListener('submit', function (e) {
                e.preventDefault(); // Evitamos el envío por defecto
                var id = document.getElementById('editCustomerId').value;
                var username = document.getElementById('editNombre').value;
                var email = document.getElementById('editEmail').value;
                var phone = document.getElementById('editTelefono').value;
                var address = document.getElementById('editDireccion').value;
                var role = document.getElementById('editRole').value;

                // Enviamos los datos del cliente al servidor usando fetch
                fetch('edit_cliente.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${id}&username=${username}&email=${email}&phone_number=${phone}&address=${address}&role=${role}`
                }).then(response => response.json()).then(data => {
                    if (data.success) {
                        location.reload(); // Recargamos la página si la actualización tuvo éxito
                    }
                });
            });

            // Llenamos el modal de eliminación con el ID del cliente a eliminar
            var eliminarClienteModal = document.getElementById('eliminarClienteModal');
            eliminarClienteModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var customerId = button.getAttribute('data-id'); 
                var botonConfirmarEliminacion = document.getElementById('botonConfirmarEliminacion');
                botonConfirmarEliminacion.setAttribute('href', 'eliminar_cliente.php?id=' + customerId);
            });
        </script>
        <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    </body>
</html>