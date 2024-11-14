<?php
require_once '../../db_config.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

$result = $conn->query("SELECT * FROM restaurant");
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
        .card-actions button {
            border-radius: 0.25rem;
        }
        .edit-btn {
            border-top-left-radius: 0.25rem;
            border-bottom-left-radius: 0.25rem;
        }
        .delete-btn {
            border-top-right-radius: 0.25rem;
            border-bottom-right-radius: 0.25rem;
        }
        .card-actions {
            display: flex;
            justify-content: flex-start;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h1>Gestión de Restaurantes</h1>
    <!-- Botón de añadir restaurante -->
    <button class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#addRestaurantModal">Añadir Restaurante</button>

    <div class="row mt-4">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= $row['name'] ?></h5>
                        <p class="card-text"><strong>Dirección:</strong> <?= $row['address'] ?></p>
                        <p class="card-text"><strong>Teléfono:</strong> <?= $row['phone_number'] ?></p>
                        <div class="card-actions">
                            <a href="#" class="btn btn-warning edit-btn" data-bs-toggle="modal" data-bs-target="#editRestaurantModal" data-id="<?= $row['id'] ?>" data-name="<?= $row['name'] ?>" data-address="<?= $row['address'] ?>" data-phone="<?= $row['phone_number'] ?>">Editar</a>
                            <a href="#" class="btn btn-danger delete-btn" data-bs-toggle="modal" data-bs-target="#deleteRestaurantModal" data-id="<?= $row['id'] ?>">Eliminar</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Modal para añadir nuevo restaurante -->
<div class="modal fade" id="addRestaurantModal" tabindex="-1" aria-labelledby="addRestaurantModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRestaurantModalLabel">Añadir Restaurante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addRestaurantForm">
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
                    <button type="submit" class="btn btn-primary">Guardar Restaurante</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar restaurante -->
<div class="modal fade" id="editRestaurantModal" tabindex="-1" aria-labelledby="editRestaurantModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRestaurantModalLabel">Editar Restaurante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editRestaurantForm">
                    <input type="hidden" id="editRestaurantId">
                    <div class="mb-3">
                        <label for="editRestaurantName" class="form-label">Nombre del Restaurante</label>
                        <input type="text" class="form-control" id="editRestaurantName" required>
                    </div>
                    <div class="mb-3">
                        <label for="editRestaurantAddress" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="editRestaurantAddress" required>
                    </div>
                    <div class="mb-3">
                        <label for="editRestaurantPhone" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="editRestaurantPhone" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para eliminar restaurante -->
<div class="modal fade" id="deleteRestaurantModal" tabindex="-1" aria-labelledby="deleteRestaurantModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteRestaurantModalLabel">Eliminar Restaurante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar este restaurante?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="deleteRestaurantButton" class="btn btn-danger">Eliminar</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Añadir nuevo restaurante
    document.getElementById('addRestaurantForm').addEventListener('submit', function(e) {
        e.preventDefault();

        var name = document.getElementById('restaurantName').value;
        var address = document.getElementById('restaurantAddress').value;
        var phone = document.getElementById('restaurantPhone').value;

        fetch('add_restaurant.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `name=${name}&address=${address}&phone_number=${phone}`
        }).then(response => response.json()).then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error al añadir el restaurante: ' + data.error);
            }
        });
    });

    // Editar restaurante
    var editRestaurantModal = document.getElementById('editRestaurantModal');
    editRestaurantModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var name = button.getAttribute('data-name');
        var address = button.getAttribute('data-address');
        var phone = button.getAttribute('data-phone');

        document.getElementById('editRestaurantId').value = id;
        document.getElementById('editRestaurantName').value = name;
        document.getElementById('editRestaurantAddress').value = address;
        document.getElementById('editRestaurantPhone').value = phone;
    });

    document.getElementById('editRestaurantForm').addEventListener('submit', function (e) {
        e.preventDefault();
        var id = document.getElementById('editRestaurantId').value;
        var name = document.getElementById('editRestaurantName').value;
        var address = document.getElementById('editRestaurantAddress').value;
        var phone = document.getElementById('editRestaurantPhone').value;

        fetch('edit_restaurant.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}&name=${name}&address=${address}&phone_number=${phone}`
        }).then(response => response.json()).then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error al editar el restaurante: ' + data.error);
            }
        });
    });

    // Eliminar restaurante
    var deleteRestaurantModal = document.getElementById('deleteRestaurantModal');
    deleteRestaurantModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var deleteButton = document.getElementById('deleteRestaurantButton');
        deleteButton.href = 'delete_restaurant.php?id=' + id;
    });
</script>

</body>
</html>
