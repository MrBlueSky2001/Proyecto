<?php
    // Incluimos la configuración de la base de datos
    require_once '../../db_config.php';
    // Iniciamos la sesión para poder acceder a las variables de sesión
    session_start();

    // Verificamos si la solicitud es de tipo POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtenemos el ID del cliente de la sesión
        $customerId = $_SESSION['user']['id'];
        // Obtenemos los datos de la reserva del formulario
        $restaurantId = $_POST['restaurant_id'];
        $reservationDate = $_POST['reservation_date'];
        $reservationTime = $_POST['reservation_time'];
        $numberOfGuests = $_POST['number_of_guests'];

        // Generamos un número de mesa aleatorio entre 1 y 20
        $tableNumber = rand(1, 20);

        // Validamos que la reserva no se haga para el mismo día
        $today = new DateTime(); // Obtenemos la fecha y hora actual
        $reservationDateTime = new DateTime($reservationDate); // Convertimos la fecha de reserva a un objeto DateTime

        // Si la fecha de la reserva es hoy o anterior, rechazamos la reserva
        if ($reservationDateTime <= $today) {
            echo json_encode(['status' => 'error', 'message' => 'No se pueden hacer reservas para el mismo día.']);
            exit; // Terminamos la ejecución del script
        }

        // Validamos que no haya más de una reserva en el mismo restaurante
        $stmt = $conn->prepare("SELECT COUNT(*) FROM Reservation WHERE customer_id = ? AND restaurant_id = ? AND reservation_date = ?");
        $stmt->bind_param("iis", $customerId, $restaurantId, $reservationDate); // Vinculamos los parámetros
        $stmt->execute(); // Ejecutamos la consulta
        $stmt->bind_result($count); // Vinculamos el resultado a la variable $count
        $stmt->fetch(); // Obtenemos el resultado
        $stmt->close(); // Cerramos la declaración

        // Si ya existe una reserva para el mismo restaurante y fecha, rechazamos la nueva reserva
        if ($count > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Ya tienes una reserva en este restaurante para esta fecha.']);
            exit; // Terminamos la ejecución del script
        }

        // Obtenemos el horario de apertura y cierre del restaurante
        $stmt = $conn->prepare("SELECT open_time, close_time FROM Restaurant WHERE id = ?");
        $stmt->bind_param("i", $restaurantId); // Vinculamos el ID del restaurante
        $stmt->execute(); // Ejecutamos la consulta
        $stmt->bind_result($openTime, $closeTime); // Vinculamos los resultados a las variables
        $stmt->fetch(); // Obtenemos los resultados
        $stmt->close(); // Cerramos la declaración

        // Creamos objetos DateTime para la hora de reserva y los horarios de apertura y cierre
        $reservationStart = new DateTime($reservationTime);
        $reservationEnd = clone $reservationStart; // Clonamos el objeto para no modificar el original
        $reservationEnd->modify('+2 hours'); // Establecemos la duración de la reserva a 2 horas
        $openTimeObj = new DateTime($openTime);
        $closeTimeObj = new DateTime($closeTime);

        // Verificamos si la hora de reserva está dentro del horario de apertura y cierre
        if ($reservationStart < $openTimeObj || $reservationEnd > $closeTimeObj) {
            echo json_encode(['status' => 'error', 'message' => 'Horario de reserva no válido.']);
            exit; // Terminamos la ejecución del script
        }

        // Insertamos la reserva en la base de datos
        $stmt = $conn->prepare("INSERT INTO Reservation (customer_id, restaurant_id, table_number, reservation_date, reservation_time, number_of_guests) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            echo json_encode(['status' => 'error', 'message' => 'Error preparando la consulta: ' . $conn->error]);
            exit; // Terminamos la ejecución del script si hay un error al preparar la consulta
        }

        // Vinculamos los parámetros para la inserción
        $stmt->bind_param("iiissi", $customerId, $restaurantId, $tableNumber, $reservationDate, $reservationTime, $numberOfGuests);

        // Ejecutamso la consulta de inserción
        if ($stmt->execute()) {
            // Si la inserción ha tenido éxito, devolvemos un mensaje de éxito con el número de mesa
            echo json_encode(['status' => 'success', 'table_number' => $tableNumber ]);
        } else {
            // Si hay un error al ejecutar la consulta, devolvemos un mensaje de error
            echo json_encode(['status' => 'error', 'message' => 'Error ejecutando la consulta: ' . $stmt->error]);
        }

        $stmt->close();
    }
?>