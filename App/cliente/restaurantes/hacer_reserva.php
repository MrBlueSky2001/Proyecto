<?php
require_once '../../db_config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerId = $_SESSION['user']['id'];
    $restaurantId = $_POST['restaurant_id'];
    $reservationDate = $_POST['reservation_date'];
    $reservationTime = $_POST['reservation_time'];
    $numberOfGuests = $_POST['number_of_guests'];

    $tableNumber = rand(1, 20);

    // Validar que la reserva no se haga para el mismo día
    $today = new DateTime();
    $reservationDateTime = new DateTime($reservationDate);

    // Si la fecha de la reserva es hoy o anterior, rechazar la reserva
    if ($reservationDateTime <= $today) {
        echo json_encode(['status' => 'error', 'message' => 'No se pueden hacer reservas para el mismo día.']);
        exit;
    }

    // Validar que no haya más de una reserva en el mismo restaurante
    $stmt = $conn->prepare("SELECT COUNT(*) FROM Reservation WHERE customer_id = ? AND restaurant_id = ? AND reservation_date = ?");
    $stmt->bind_param("iis", $customerId, $restaurantId, $reservationDate);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Ya tienes una reserva en este restaurante para esta fecha.']);
        exit;
    }

    // Obtener el horario de apertura y cierre del restaurante
    $stmt = $conn->prepare("SELECT open_time, close_time FROM Restaurant WHERE id = ?");
    $stmt->bind_param("i", $restaurantId);
    $stmt->execute();
    $stmt->bind_result($openTime, $closeTime);
    $stmt->fetch();
    $stmt->close();

    $reservationStart = new DateTime($reservationTime);
    $reservationEnd = clone $reservationStart;
    $reservationEnd->modify('+2 hours');
    $openTimeObj = new DateTime($openTime);
    $closeTimeObj = new DateTime($closeTime);

    if ($reservationStart < $openTimeObj || $reservationEnd > $closeTimeObj) {
        echo json_encode(['status' => 'error', 'message' => 'Horario de reserva no válido.']);
        exit;
    }

    // Insertar la reserva
    $stmt = $conn->prepare("INSERT INTO Reservation (customer_id, restaurant_id, table_number, reservation_date, reservation_time, number_of_guests) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Error preparando la consulta: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("iiissi", $customerId, $restaurantId, $tableNumber, $reservationDate, $reservationTime, $numberOfGuests);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'table_number' => $tableNumber]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error ejecutando la consulta: ' . $stmt->error]);
    }

    $stmt->close();
}
?>