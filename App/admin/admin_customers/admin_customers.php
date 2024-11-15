<?php
require_once '../../db_config.php';
session_start();

// Verificar que el usuario esté autenticado y sea administrador
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

// Consultar la lista de clientes
$result = $conn->query("SELECT id, username, phone_number, address, role FROM customer");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestión de Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1>Gestión de Clientes</h1>

        <!-- Tabla de clientes -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de Usuario</th>
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
                        <td><?= $row['username'] ?></td>
                        <td><?= $row['phone_number'] ?></td>
                        <td><?= $row['address'] ?></td>
                        <td><?= $row['role'] ?></td>
                        <td>
                            <!-- Enlace para editar con modal -->
                            <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#editCustomerModal" data-id="<?= $row['id'] ?>" data-username="<?= $row['username'] ?>" data-phone="<?= $row['phone_number'] ?>" data-address="<?= $row['address'] ?>" data-role="<?= $row['role'] ?>">Editar</a> |
                            <!-- Enlace para eliminar con modal de confirmación -->
                            <a href="#" class="text-danger" data-bs-toggle="modal" data-bs-target="#deleteCustomerModal" data-id="<?= $row['id'] ?>">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Modal de editar cliente -->
        <div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCustomerModalLabel">Editar Cliente</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
        <div class="modal fade" id="deleteCustomerModal" tabindex="-1" aria-labelledby="deleteCustomerModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteCustomerModalLabel">Eliminar Cliente</h5>
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
        var editCustomerModal = document.getElementById('editCustomerModal');
        editCustomerModal.addEventListener('show.bs.modal', function (event) {
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

            fetch('edit_customer.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}&username=${username}&phone_number=${phone}&address=${address}&role=${role}`
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        });

        var deleteCustomerModal = document.getElementById('deleteCustomerModal');
        deleteCustomerModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var customerId = button.getAttribute('data-id'); 
            var deleteConfirmBtn = document.getElementById('deleteConfirmBtn');
            deleteConfirmBtn.setAttribute('href', 'delete_customer.php?id=' + customerId);
        });
    </script>
</body>
</html>