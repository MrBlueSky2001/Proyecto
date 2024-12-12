<?php
    require_once '../../db_config.php';
    
    // Iniciamos la sesión, lo cual es necesario para verificar que el usuario esté autenticado
    session_start();

    // Verificamos que el usuario esté logueado y que tenga rol de "admin"
    // Si no está logueado o su rol no es "admin", redirigimos a la página de login
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        header('Location: ../../login.php');
        exit(); // Terminamos el script para evitar que se ejecute el resto del código
    }

    // Verificamos si la solicitud es un POST, lo que indica que se está enviando un formulario con los datos de un nuevo restaurante
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recogemos los datos enviados desde el formulario y los asigna a variables
        $name = $_POST['name'];
        $address = $_POST['address'];
        $phone_number = $_POST['phone_number'];
        $email = $_POST['email'];
        $description = $_POST['description'];
        $open_time = $_POST['open_time'];
        $close_time = $_POST['close_time'];

        // Preparamos la sentencia SQL para insertar los datos en la tabla "restaurant"
        $stmt = $conn->prepare("INSERT INTO restaurant (name, address, phone_number, email, description, open_time, close_time) VALUES (?, ?, ?, ?, ?, ?, ?)");

        // Vinculamos los parámetros de la consulta con las variables de PHP, indicando que son de tipo string (s)
        $stmt->bind_param("sssssss", $name, $address, $phone_number, $email, $description, $open_time, $close_time);

        // Ejecutamos la consulta SQL. Si la inserción tuvo éxito, devolvemos un JSON con éxito; si falla, devolvemos un mensaje de error
        if ($stmt->execute()) {
            echo json_encode(['success' => true]); // Respondemos con éxito
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]); // Respondemos con un error si la consulta falla
        }
    }
?>