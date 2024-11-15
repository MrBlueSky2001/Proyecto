<?php
require_once '../../db_config.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

// Obtener los restaurantes y sus comidas
$restaurants = $conn->query("SELECT * FROM restaurant");

// Obtener las categorías de alimentos
$categories = $conn->query("SELECT * FROM foodcategory");
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
                                    <th>Categoría</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Usamos INNER JOIN para obtener el category_name
                                $foods = $conn->query("SELECT food.id, food.name, food.description, foodcategory.category_name
                                                       FROM food
                                                       INNER JOIN foodcategory ON food.category_id = foodcategory.id
                                                       WHERE food.restaurant_id = " . $restaurant['id']);
                                while ($food = $foods->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $food['id'] ?></td>
                                        <td><?= $food['name'] ?></td>
                                        <td><?= $food['description'] ?></td>
                                        <td><?= $food['category_name'] ?></td>
                                        <td>
                                            <!-- Botones para abrir los modales de editar y eliminar -->
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editFoodModal" data-id="<?= $food['id'] ?>" data-name="<?= $food['name'] ?>" data-description="<?= $food['description'] ?>" data-category="<?= $food['category_name'] ?>">Editar</button>
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
                    <div class="mb-3">
                        <label for="addFoodCategory" class="form-label">Categoría</label>
                        <select class="form-select" id="addFoodCategory" name="category_id" required>
                            <?php while ($category = $categories->fetch_assoc()): ?>
                                <option value="<?= $category['id'] ?>"><?= $category['category_name'] ?></option>
                            <?php endwhile; ?>
                        </select>
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

<!-- Modal para mostrar mensajes de éxito o error -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel">Mensaje</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalMessageContent">
                <!-- El contenido del mensaje se llenará aquí -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

// Función para mostrar el modal con un mensaje
function showMessage(message, isSuccess) {
    const modalMessageContent = document.getElementById('modalMessageContent');
    modalMessageContent.textContent = message;  // Inyectamos el mensaje
    const modal = new bootstrap.Modal(document.getElementById('messageModal'));

    // Cambiar el color del modal dependiendo de si es un mensaje de éxito o error
    if (isSuccess) {
        document.querySelector('.modal-title').textContent = "Éxito";
        document.querySelector('.modal-header').style.backgroundColor = "#28a745";  // Verde para éxito
    } else {
        document.querySelector('.modal-title').textContent = "Error";
        document.querySelector('.modal-header').style.backgroundColor = "#dc3545";  // Rojo para error
    }

    modal.show();
}

// Añadir comida
var addFoodModal = document.getElementById('addFoodModal');
addFoodModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    document.getElementById('addRestaurantId').value = button.getAttribute('data-restaurant-id');
});

document.getElementById('addFoodForm').addEventListener('submit', function (e) {
    e.preventDefault();  // Prevenir que se recargue la página al enviar el formulario
    
    // Obtener los datos del formulario
    var formData = new FormData(this);
    
    // Hacer la solicitud al servidor usando fetch
    fetch('add_food.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) // Asegurar de que la respuesta es JSON
    .then(data => {
        if (data.success) {
            // Si la respuesta tiene un mensaje de éxito
            showMessage(data.success, true);
            location.reload(); // Recargar la página para reflejar el nuevo dato
        } else {
            // Si la respuesta tiene un mensaje de error
            showMessage(data.error, false);
        }
    })
    .catch(error => {
        // Si hay un error en la solicitud o en la respuesta
        console.error('Error al procesar la solicitud:', error);
        showMessage('Ocurrió un error inesperado. Por favor, intenta de nuevo.', false);
    });
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
    e.preventDefault();  // Prevenir la recarga de la página al enviar el formulario

    // Obtener los datos del formulario de edición
    var formData = new FormData(this);

    // Hacer la solicitud al servidor usando fetch
    fetch('edit_food.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) // Asegurar de que la respuesta es JSON
    .then(data => {
        if (data.success) {
            // Si la respuesta tiene un mensaje de éxito
            showMessage(data.success, true);
            location.reload(); // Recargar la página para reflejar los cambios
        } else {
            // Si la respuesta tiene un mensaje de error
            showMessage(data.error, false);
        }
    })
    .catch(error => {
        // Si hay un error en la solicitud o en la respuesta
        console.error('Error al procesar la solicitud:', error);
        showMessage('Ocurrió un error inesperado. Por favor, intenta de nuevo.', false);
    });
});

// Eliminar comida
var deleteFoodModal = document.getElementById('deleteFoodModal');
deleteFoodModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    document.getElementById('confirmDeleteFood').setAttribute('data-id', button.getAttribute('data-id'));
});

document.getElementById('confirmDeleteFood').addEventListener('click', function () {
    var id = this.getAttribute('data-id');

    // Hacer la solicitud al servidor para eliminar la comida
    fetch('delete_food.php?id=' + id, { method: 'GET' })
    .then(response => response.json()) // Asegurar de que la respuesta es JSON
    .then(data => {
        if (data.success) {
            // Si la respuesta tiene un mensaje de éxito
            showMessage(data.success, true);
            location.reload(); // Recargar la página para reflejar los cambios
        } else {
            // Si la respuesta tiene un mensaje de error
            showMessage(data.error, false);
        }
    })
    .catch(error => {
        // Si hay un error en la solicitud o en la respuesta
        console.error('Error al procesar la solicitud:', error);
        showMessage('Ocurrió un error inesperado. Por favor, intenta de nuevo.', false);
    });
});

});

</script>
</body>
</html>