<?php
    require_once '../../db_config.php';

    // Iniciamos la sesión para verificar si el usuario está autenticado
    session_start();

    // Establecemos el tipo de contenido de la respuesta a JSON, ya que se espera una respuesta en formato JSON
    header('Content-Type: application/json');

    // Verificamos si el usuario está logueado y si tiene el rol de 'admin'. Si no es así, redirigimos al login
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        header('Location: ../../login.php');
        exit(); // Terminamos la ejecución del script
    }

    // Obtenemos los datos enviados a través del método POST
    $id = $_POST['id']; // ID de la comida que vamos a actualizar
    $name = $_POST['name']; // Nombre de la comida
    $description = $_POST['description']; // Descripción de la comida
    $price = $_POST['price']; // Precio de la comida

    // Verificamos que todos los campos necesarios han sido enviados y no están vacíos
    if (empty($id) || empty($name) || empty($description) || empty($price)) {
        // Si algún campo está vacío, devolvemos un mensaje de error en formato JSON
        echo json_encode(['error' => 'Todos los campos son obligatorios']);
        exit(); // Terminamos la ejecución del script
    }

    // Preparamos la consulta SQL para actualizar la comida en la base de datos
    $stmt = $conn->prepare("UPDATE food SET name = ?, description = ?, price = ? WHERE id = ?");

    // Vinculamos los parámetros de la consulta a las variables recibidas
    // 'ssdi' indica que el nombre y la descripción son cadenas de texto ('s'), el precio es un número con decimales ('d') y el id es un entero ('i')
    $stmt->bind_param('ssdi', $name, $description, $price, $id);

    // Ejecutamos la consulta preparada
    if ($stmt->execute()) {
        // Si la consulta se ejecutó con éxito, devolvemos un mensaje de éxito en formato JSON
        echo json_encode(['success' => 'Comida actualizada con éxito']);
    } else {
        // Si hubo un error al ejecutar la consulta, devolvemos un mensaje de error en formato JSON
        echo json_encode(['error' => 'Error al actualizar la comida']);
    }

    // Cerramos la sentencia SQL y la conexión con la base de datos
    $stmt->close();
    $conn->close();
?>