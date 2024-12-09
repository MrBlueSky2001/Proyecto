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
        <style>
            body {
                background-color: #F8F5F2; /* Fondo de la página */
            }
            h1 {
                color: #3498db; /* Título en color azul */
                text-align: center;
                margin-top: 20px;
            }
            .accordion-button {
                background-color: white; /* Fondo blanco para el botón del acordeón */
                color: #3498db; /* Texto en azul */
                border: 1px solid #3498db; /* Borde azul */
            }
            .accordion-button:not(.collapsed) {
                background-color: #e7f0ff; /* Fondo claro cuando está expandido */
                color: #2980b9; /* Texto en azul más oscuro */
            }
            .accordion-item {
                border: 1px solid #3498db; /* Borde azul para el acordeón */
                border-radius: 0.5rem; /* Bordes redondeados */
                margin-bottom: 1rem; /* Espacio entre los acordeones */
            }
            .modal-header {
                background-color: #3498db; /* Encabezado del modal en azul */
                color: white; /* Texto blanco en el encabezado */
            }
            .modal-footer .btn {
                background-color: #3498db; /* Botones en el pie del modal */
                color: white; /* Texto blanco en los botones */
            }
            .modal-footer .btn:hover {
                background-color: #2980b9; /* Color al pasar el ratón sobre los botones */
            }
        </style>
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
                                            <th>Precio</th>
                                            <th>Categoría</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Usamos INNER JOIN para obtener el category_name
                                        $foods = $conn->query("SELECT food.id, food.name, food.description, food.price, foodcategory.category_name
                                                            FROM food
                                                            INNER JOIN foodcategory ON food.category_id = foodcategory.id
                                                            WHERE food.restaurant_id = " . $restaurant['id']);
                                        while ($food = $foods->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $food['id'] ?></td>
                                                <td><?= $food['name'] ?></td>
                                                <td><?= $food['description'] ?></td>
                                                <td><?= $food['price'] ?> €</td> <!-- Mostrar el precio -->
                                                <td><?= $food['category_name'] ?></td>
                                                <td>
                                                    <!-- Botones para abrir los modales de editar y eliminar -->
                                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editarComidaModal" data-id="<?= $food['id'] ?>" data-name="<?= $food['name'] ?>" data-description="<?= $food['description'] ?>" data-price="<?= $food['price'] ?>" data-category="<?= $food['category_name'] ?>">Editar</button>
                                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#eliminarComidaModal" data-id="<?= $food['id'] ?>">Eliminar</button>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                                <!-- Botón para abrir el modal de añadir comida -->
                                <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#anadirComidaModal" data-restaurant-id="<?= $restaurant['id'] ?>">Añadir Comida</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Modal para añadir comida -->
        <div class="modal fade" id="anadirComidaModal" tabindex="-1" aria-labelledby="anadirComidaModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="anadirComidaModalLabel">Añadir Comida</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="anadirComidaForm">
                            <input type="hidden" id="anadirRestauranteId" name="restaurant_id">
                            <div class="mb-3">
                                <label for="anadirComidaNombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="anadirComidaNombre" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="anadirComidaDescripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="anadirComidaDescripcion" name="description" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="anadirComidaPrecio" class="form-label">Precio (€)</label>
                                <input type="number" class="form-control" id="anadirComidaPrecio" name="price" step="0.01" required>
                            </div>
                            <div class="mb-3">
                                <label for="anadirComidaCategoria" class="form-label">Categoría</label>
                                <select class="form-select" id="anadirComidaCategoria" name="category_id" required>
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
        <div class="modal fade" id="editarComidaModal" tabindex="-1" aria-labelledby="editarComidaModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarComidaModalLabel">Editar Comida</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editarComidaForm">
                            <input type="hidden" id="editarComidaId" name="id">
                            <div class="mb-3">
                                <label for="editarComidaNombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="editarComidaNombre" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="editarComidaDescripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="editarComidaDescripcion" name="description" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="editarComidaPrecio" class="form-label">Precio (€)</label>
                                <input type="number" class="form-control" id="editarComidaPrecio" name="price" step="0.01" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para eliminar comida -->
        <div class="modal fade" id="eliminarComidaModal" tabindex="-1" aria-labelledby="eliminarComidaModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="eliminarComidaModalLabel">Eliminar Comida</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Estás seguro de que deseas eliminar esta comida?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" id="confirmaEliminarComida" class="btn btn-danger">Eliminar</button>
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
                        <!-- El contenido del mensaje se llenará aquí de manera dinámica-->
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
        function mostrarMensaje(message, isSuccess) {
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
        var anadirComidaModal = document.getElementById('anadirComidaModal');
        anadirComidaModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            document.getElementById('anadirRestauranteId').value = button.getAttribute('data-restaurant-id');
        });

        document.getElementById('anadirComidaForm').addEventListener('submit', function (e) {
            e.preventDefault();  // Prevenir que se recargue la página al enviar el formulario
            
            // Obtener los datos del formulario
            var formData = new FormData(this);
            
            // Hacer la solicitud al servidor usando fetch
            fetch('anadir_comida.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json()) // Asegurar de que la respuesta es JSON
            .then(data => {
                if (data.success) {
                    // Si la respuesta tiene un mensaje de éxito
                    mostrarMensaje(data.success, true);
                    location.reload(); // Recargar la página para reflejar el nuevo dato
                } else {
                    // Si la respuesta tiene un mensaje de error
                    mostrarMensaje(data.error, false);
                }
            })
            .catch(error => {
                // Si hay un error en la solicitud o en la respuesta
                console.error('Error al procesar la solicitud:', error);
                mostrarMensaje('Ocurrió un error inesperado. Por favor, intenta de nuevo.', false);
            });
        });

        // Editar comida
        var editarComidaModal = document.getElementById('editarComidaModal');
        editarComidaModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            document.getElementById('editarComidaId').value = button.getAttribute('data-id');
            document.getElementById('editarComidaNombre').value = button.getAttribute('data-name');
            document.getElementById('editarComidaDescripcion').value = button.getAttribute('data-description');
            document.getElementById('editarComidaPrecio').value = button.getAttribute('data-price');
        });

        document.getElementById('editarComidaForm').addEventListener('submit', function (e) {
            e.preventDefault();  // Prevenir la recarga de la página al enviar el formulario

            // Obtener los datos del formulario de edición
            var formData = new FormData(this);

            // Hacer la solicitud al servidor usando fetch
            fetch('edit_comida.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json()) // Asegurar de que la respuesta es JSON
            .then(data => {
                if (data.success) {
                    // Si la respuesta tiene un mensaje de éxito
                    mostrarMensaje(data.success, true);
                    location.reload(); // Recargar la página para reflejar los cambios
                } else {
                    // Si la respuesta tiene un mensaje de error
                    mostrarMensaje(data.error, false);
                }
            })
            .catch(error => {
                // Si hay un error en la solicitud o en la respuesta
                console.error('Error al procesar la solicitud:', error);
                mostrarMensaje('Ocurrió un error inesperado. Por favor, intenta de nuevo.', false);
            });
        });

        // Eliminar comida
        var eliminarComidaModal = document.getElementById('eliminarComidaModal');
        eliminarComidaModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            document.getElementById('confirmaEliminarComida').setAttribute('data-id', button.getAttribute('data-id'));
        });

        document.getElementById('confirmaEliminarComida').addEventListener('click', function () {
            var id = this.getAttribute('data-id');

            // Hacer la solicitud al servidor para eliminar la comida
            fetch('eliminar_comida.php?id=' + id, { method: 'GET' })
            .then(response => response.json()) // Asegurar de que la respuesta es JSON
            .then(data => {
                if (data.success) {
                    // Si la respuesta tiene un mensaje de éxito
                    mostrarMensaje(data.success, true);
                    location.reload(); // Recargar la página para reflejar los cambios
                } else {
                    // Si la respuesta tiene un mensaje de error
                    mostrarMensaje(data.error, false);
                }
            })
            .catch(error => {
                // Si hay un error en la solicitud o en la respuesta
                console.error('Error al procesar la solicitud:', error);
                mostrarMensaje('Ocurrió un error inesperado. Por favor, intenta de nuevo.', false);
            });
        });

        });

        </script>
    </body>
</html>