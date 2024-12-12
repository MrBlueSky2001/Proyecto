<?php
    // Incluimos el archivo de configuración de la base de datos para establecer la conexión
    require_once 'db_config.php';

    // Verificamos si el formulario se ha enviado usando el método POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtenemos el nombre de usuario enviado desde el formulario
        $username = $_POST['username'];

        // La función password_hash genera un hash seguro de la contraseña
        $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

        // Preparamos una consulta SQL para actualizar la contraseña del usuario
        $stmt = $conn->prepare('UPDATE customer SET password = ? WHERE username = ?');
        // Vinculamos los parámetros a la consulta SQL: 
        // - el nuevo hash de la contraseña ($new_password)
        // - el nombre de usuario ($username)
        $stmt->bind_param("ss", $new_password, $username);

        // Ejecutamos la consulta preparada
        if ($stmt->execute()) {
            // Si la ejecución ha tenido éxito, mostramos un mensaje al usuario
            // y lo redirigimos a la página de inicio de sesión
            echo "<script>alert('Contraseña actualizada con éxito.'); window.location.href = 'login.php';</script>";
        } else {
            // Si ocurre un error durante la actualización, mostramos el mensaje de error
            echo "Error al actualizar la contraseña: " . $stmt->error;
        }
    }
?>
