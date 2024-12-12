<?php
    // Iniciamos la sesión para poder acceder a las variables de sesión
    session_start();
    
    // Incluimos la configuración de la base de datos
    require_once '../../db_config.php';

    // Verificamos si el usuario está autenticado, si no, redirige a la página de inicio de sesión
    if (!isset($_SESSION['user'])) {
        header("Location: ../../login.php");
        exit();
    }

    // Obtenemos los datos del usuario de la sesión
    $user = $_SESSION['user'];
    $error = ''; // Inicializamos la variable de error
    $success = ''; // Inicializamos la variable de éxito

    // Verificamos si se ha enviado un formulario mediante el método POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtenemos y sanitizamos los datos del formulario
        $phone_number = htmlspecialchars($_POST['phone_number']);
        $address = htmlspecialchars($_POST['address']);
        $email = htmlspecialchars($_POST['email']);

        // Preparamos una consulta SQL para actualizar los datos del cliente en la base de datos
        $stmt = $conn->prepare("UPDATE customer SET phone_number = ?, address = ?, email = ? WHERE id = ?");
        // Vinculamos los parámetros a la consulta (tipo de datos: s = string, i = integer)
        $stmt->bind_param("sssi", $phone_number, $address, $email, $user['id']);

        // Ejecutamos la consulta
        if ($stmt->execute()) {
            $success = "Perfil actualizado con éxito.";
            $user['phone_number'] = $phone_number;
            $user['address'] = $address;
            $user['email'] = $email;
            $_SESSION['user'] = $user; // Guardamos los nuevos datos en la sesión

            // Redirigimos a la página de perfil de usuario con un mensaje de éxito en la URL
            header("Location: perfil_usuario.php?success=" . urlencode($success));
            exit(); // Terminamos la ejecución del script para evitar el reenvío del formulario
        } else {
            $error = "Error al actualizar el perfil.";
        }
        $stmt->close();
    }
?>