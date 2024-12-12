<?php
    require_once '../../db_config.php';

    // Verificamos si se ha recibido el parámetro 'id' a través de la URL (GET)
    if (isset($_GET['id'])) {
        // Obtenemos el ID del cliente desde la URL
        $id = $_GET['id'];

        // Preparamos una sentencia SQL para eliminar un cliente de la base de datos
        // Usamos un marcador de posición (?), que se reemplazará por el ID del cliente
        $stmt = $conn->prepare("DELETE FROM customer WHERE id = ?");
        
        // Vinculamos el valor de $id al marcador de posición de la consulta SQL
        // El tipo "i" indica que el valor es un entero (ID del cliente)
        $stmt->bind_param("i", $id);
        
        // Ejecutamos la sentencia SQL para eliminar el cliente
        if ($stmt->execute()) {
            // Si la eliminación tuvo éxito, redirigimos al usuario a la página de gestión de clientes
            header('Location: admin_clientes.php');
            exit();  // Detenemos la ejecución del script después de la redirección
        }
    }
?>