<?php
require_once '../../db_config.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

// Obtener los restaurantes y sus comidas
$restaurants = $conn->query("SELECT * FROM restaurant");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Comidas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Gestión de Comidas</h1>

    <?php while ($restaurant = $restaurants->fetch_assoc()): ?>
        <div class="accordion mb-3" id="accordion<?= $restaurant['id'] ?>">
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading<?= $restaurant['id'] ?>">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $restaurant['id'] ?>" aria-expanded="true" aria-controls="collapse<?= $restaurant['id'] ?>">
                        <?= $restaurant['name'] ?>
                    </button>
                </h2>
                <div id="collapse<?= $restaurant['id'] ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $restaurant['id'] ?>" data-bs-parent="#accordion<?= $restaurant['id'] ?>">
                    <div class="accordion-body">
                        <!-- Tabla de comidas para el restaurante actual -->
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $foods = $conn->query("SELECT * FROM food WHERE restaurant_id = " . $restaurant['id']);
                                while ($food = $foods->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $food['id'] ?></td>
                                        <td><?= $food['name'] ?></td>
                                        <td><?= $food['description'] ?></td>
                                        <td>
                                            <!-- Botones para abrir los modales de editar y eliminar -->
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editFoodModal" data-id="<?= $food['id'] ?>" data-name="<?= $food['name'] ?>" data-description="<?= $food['description'] ?>">Editar</button>
                                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteFoodModal" data-id="<?= $food['id'] ?>">Eliminar</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        <!-- Botón para abrir el modal de añadir comida -->
                        <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addFoodModal" data-restaurant-id="<?= $restaurant['id'] ?>">Añadir Comida</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<!-- Modal para añadir comida -->
<div class="modal fade" id="addFoodModal" tabindex="-1" aria-labelledby="addFoodModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addFoodModalLabel">Añadir Comida</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addFoodForm">
                    <input type="hidden" id="addRestaurantId" name="restaurant_id">
                    <div class="mb-3">
                        <label for="addFoodName" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="addFoodName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="addFoodDescription" class="form-label">Descripción</label>
                        <textarea class="form-control" id="addFoodDescription" name="description" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar comida -->
<div class="modal fade" id="editFoodModal" tabindex="-1" aria-labelledby="editFoodModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editFoodModalLabel">Editar Comida</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editFoodForm">
                    <input type="hidden" id="editFoodId" name="id">
                    <div class="mb-3">
                        <label for="editFoodName" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="editFoodName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editFoodDescription" class="form-label">Descripción</label>
                        <textarea class="form-control" id="editFoodDescription" name="description" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para eliminar comida -->
<div class="modal fade" id="deleteFoodModal" tabindex="-1" aria-labelledby="deleteFoodModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteFoodModalLabel">Eliminar Comida</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar esta comida?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="confirmDeleteFood" class="btn btn-danger">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Variables para manejar los modales y formularios
document.addEventListener('DOMContentLoaded', function () {
    // Añadir comida
    var addFoodModal = document.getElementById('addFoodModal');
    addFoodModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        document.getElementById('addRestaurantId').value = button.getAttribute('data-restaurant-id');
    });

    document.getElementById('addFoodForm').addEventListener('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        fetch('add_food.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => location.reload());
    });

    // Editar comida
    var editFoodModal = document.getElementById('editFoodModal');
    editFoodModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        document.getElementById('editFoodId').value = button.getAttribute('data-id');
        document.getElementById('editFoodName').value = button.getAttribute('data-name');
        document.getElementById('editFoodDescription').value = button.getAttribute('data-description');
    });

    document.getElementById('editFoodForm').addEventListener('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        fetch('edit_food.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => location.reload());
    });

    // Eliminar comida
    var deleteFoodModal = document.getElementById('deleteFoodModal');
    deleteFoodModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        document.getElementById('confirmDeleteFood').setAttribute('data-id', button.getAttribute('data-id'));
    });

    document.getElementById('confirmDeleteFood').addEventListener('click', function () {
        var id = this.getAttribute('data-id');
        fetch('delete_food.php?id=' + id, { method: 'GET' })
            .then(response => response.json())
            .then(data => location.reload());
    });
});
</script>
</body>
</html>
