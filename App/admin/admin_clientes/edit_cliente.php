<?php
    require_once '../../db_config.php';

    // Verificamos si la solicitud es de tipo POST (cuando se envían datos a través de un formulario)
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Obtenemos los datos enviados desde el formulario a través de POST
        $id = $_POST['id'];                // ID del cliente que vamos a actualizar
        $username = $_POST['username'];    // Nombre de usuario
        $email = $_POST['email'];          // Correo electrónico
        $phone_number = $_POST['phone_number']; // Número de teléfono
        $address = $_POST['address'];      // Dirección
        $role = $_POST['role'];            // Rol del usuario (admin o customer)
        
        // Prepararmos una sentencia SQL para actualizar los datos del cliente
        $stmt = $conn->prepare("UPDATE customer SET username = ?, email = ?, phone_number = ?, address = ?, role = ? WHERE id = ?");
        
        // Vinculamos los valores obtenidos con los parámetros de la sentencia SQL
        // Los valores se pasan como parámetros de tipo "s" (string) y "i" (entero) en el caso del ID
        $stmt->bind_param("sssssi", $username, $email, $phone_number, $address, $role, $id);
        
        // Ejecutamos la sentencia SQL y verificar si tuvo éxito
        if ($stmt->execute()) {
            // Si la actualización tuvo éxito, respondemos con un JSON indicando el éxito
            echo json_encode(['success' => true]);
        } else {
            // Si la actualización falló, respondemos con un JSON indicando el error
            echo json_encode(['success' => false]);
        }
    }
?>