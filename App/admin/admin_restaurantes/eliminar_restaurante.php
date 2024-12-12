<?php
    require_once '../../db_config.php';

    // Verificamos si existe el parámetro 'id' en la URL mediante el método GET
    // Este 'id' es el identificador del restaurante que deseamos eliminar
    if (isset($_GET['id'])) {
        // Asignamos el valor del parámetro 'id' recibido en la URL a la variable $id
        $id = $_GET['id'];

        // Preparamos la consulta SQL para eliminar un restaurante con el ID especificado
        // La consulta DELETE eliminará un registro de la tabla 'restaurant' que tenga el 'id' igual al valor pasado
        $stmt = $conn->prepare("DELETE FROM restaurant WHERE id = ?");

        // Vinculamos el parámetro del ID a la consulta, indicando que es un valor entero ('i' representa entero)
        $stmt->bind_param("i", $id);

        // Ejecutamos la consulta SQL preparada. Si la ejecución tuvo éxito, se redirige al administrador de restaurantes.
        if ($stmt->execute()) {
            // Redirigimos a la página 'admin_restaurantes.php' si el restaurante fue eliminado correctamente
            // El 'exit()' asegura que el script se detenga aquí después de la redirección
            header('Location: admin_restaurantes.php');
            exit();
        }
    }
?>