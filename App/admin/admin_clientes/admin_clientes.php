<?php
    require_once '../../db_config.php';
    session_start();

    // Verificar que el usuario esté autenticado y sea administrador
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        header('Location: ../../login.php');
        exit();
    }

    // Consultar la lista de clientes
    $result = $conn->query("SELECT id, username, dni, phone_number, address, role FROM customer");
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
                background-color: #F8F5F2; /* Fondo de la página */
            }
            .container {
                margin-top: 50px;
            }
            h1 {
                color: #3498db; /* Título en color azul */
                text-align: center;
                margin-bottom: 20px;
            }
            .table {
                background-color: white; /* Fondo blanco para la tabla */
                border-radius: 8px; /* Bordes redondeados */
                overflow: hidden; /* Para que los bordes redondeados se apliquen */
            }
            .table th {
                background-color: #3498db; /* Encabezado de la tabla en azul */
                color: white; /* Texto blanco en el encabezado */
            }
            .table td a {
                text-decoration: none; /* Sin subrayado en los enlaces */
            }
            .table td a:hover {
                text-decoration: underline; /* Subrayado en hover */
            }
            .modal-header {
                background-color: #3498db; /* Encabezado del modal en azul */
                color: white; /* Texto blanco en el encabezado del modal */
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
                        <th>DNI</th> <!-- Nueva columna para DNI -->
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
                            <td><?= htmlspecialchars($row['dni']) ?></td> <!-- Mostrar DNI -->
                            <td><?= htmlspecialchars($row['phone_number']) ?></td>
                            <td><?= htmlspecialchars($row['address']) ?></td>
                            <td><?= htmlspecialchars($row['role']) ?></td>
                            <td>
                                <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#editClienteModal" data-id="<?= $row['id'] ?>" data-username="<?= htmlspecialchars($row['username']) ?>" data-dni="<?= htmlspecialchars($row['dni']) ?>" data-phone="<?= htmlspecialchars($row['phone_number']) ?>" data-address="<?= htmlspecialchars($row['address']) ?>" data-role="<?= htmlspecialchars($row['role']) ?>">
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
                            <button type <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editCustomerForm">
                                <input type="hidden" id="editCustomerId">
                                <div class="mb-3">
                                    <label for="editUsername" class="form-label">Nombre de Usuario</label>
                                    <input type="text" class="form-control" id="editUsername" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editPhone" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" id="editPhone" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editAddress" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="editAddress" required>
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
                            <a href="#" id="deleteConfirmBtn" class="btn btn-danger">Eliminar</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <script>
            // Llenar el modal de editar con los datos del cliente
            var editClienteModal = document.getElementById('editClienteModal');
            editClienteModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget; // Botón que activó el modal
                var id = button.getAttribute('data-id');
                var username = button.getAttribute('data-username');
                var phone = button.getAttribute('data-phone');
                var address = button.getAttribute('data-address');
                var role = button.getAttribute('data-role');

                document.getElementById('editCustomerId').value = id;
                document.getElementById('editUsername').value = username;
                document.getElementById('editPhone').value = phone;
                document.getElementById('editAddress').value = address;
                document.getElementById('editRole').value = role; 
            });

            document.getElementById('editCustomerForm').addEventListener('submit', function (e) {
                e.preventDefault();
                var id = document.getElementById('editCustomerId').value;
                var username = document.getElementById('editUsername').value;
                var phone = document.getElementById('editPhone').value;
                var address = document.getElementById('editAddress').value;
                var role = document.getElementById('editRole').value;

                fetch('edit_cliente.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${id}&username=${username}&phone_number=${phone}&address=${address}&role=${role}`
                }).then(response => response.json()).then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            });

            var eliminarClienteModal = document.getElementById('eliminarClienteModal');
            eliminarClienteModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var customerId = button.getAttribute('data-id'); 
                var deleteConfirmBtn = document.getElementById('deleteConfirmBtn');
                deleteConfirmBtn.setAttribute('href', 'eliminar_cliente.php?id=' + customerId);
            });
        </script>
        <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    </body>
</html>