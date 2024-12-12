<?php
    require_once '../../db_config.php';

    // Verificamos si el método de solicitud es POST, lo que indica que se está enviando un formulario con datos para actualizar un restaurante
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Recogemos los datos enviados desde el formulario (ID del restaurante y los datos actualizados)
        $id = $_POST['id'];  // ID del restaurante que se quiere actualizar
        $name = $_POST['name'];  // Nombre actualizado del restaurante
        $address = $_POST['address'];  // Dirección actualizada del restaurante
        $phone_number = $_POST['phone_number'];  // Número de teléfono actualizado del restaurante
        $email = $_POST['email'];  // Correo electrónico actualizado del restaurante
        $description = $_POST['description'];  // Descripción actualizada del restaurante
        $open_time = $_POST['open_time'];  // Hora de apertura actualizada
        $close_time = $_POST['close_time'];  // Hora de cierre actualizada

        // Preparamos la consulta SQL para actualizar los datos del restaurante en la base de datos
        // Utilizamos una sentencia SQL con parámetros placeholders para evitar inyecciones SQL
        $stmt = $conn->prepare("UPDATE restaurant SET name = ?, address = ?, phone_number = ?, email = ?, description = ?, open_time = ?, close_time = ? WHERE id = ?");

        // Vinculamos los valores de las variables PHP a los parámetros de la consulta SQL.
        // El tipo de los parámetros se define con la cadena "sssssssi", donde 's' significa string y 'i' significa entero (para el ID).
        $stmt->bind_param("sssssssi", $name, $address, $phone_number, $email, $description, $open_time, $close_time, $id);

        // Ejecutamos la consulta SQL. Si la ejecución tuvo éxito, devolvemos un JSON con success: true. Si falla, devolvemos success: false.
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);  // Respuesta en caso de éxito
        } else {
            echo json_encode(['success' => false]);  // Respuesta en caso de error
        }
    }
?>