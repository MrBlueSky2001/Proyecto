<?php
    require_once '../../db_config.php';
    require '../../vendor/autoload.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'];
        $status = $_POST['status'];

        // Actualiza el estado de la reserva
        $stmt = $conn->prepare("UPDATE reservation SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);

        if ($stmt->execute()) {
            if ($status === 'confirmado' || $status === 'cancelado') {

                $result = $conn->query("SELECT customer.email, customer.username, restaurant.name 
                                        FROM reservation 
                                        JOIN customer ON reservation.customer_id = customer.id 
                                        JOIN restaurant ON reservation.restaurant_id = restaurant.id
                                        WHERE reservation.id = $id");
                $row = $result->fetch_assoc();
                $customerEmail = $row['email'];
                $customerName = $row['username'];
                $restaurantName = $row['name'];

                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'smatisa951@iesmartinezm.es';
                    $mail->Password = 'MrBlueSky';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('smatisa951@iesmartinezm.es', 'Reserva Restaurant');
                    $mail->addAddress($customerEmail, $customerName);

                    $mail->isHTML(true);
                    $mail->Subject = "Estado de tu reserva en $restaurantName";
                    $mail->Body    = "<h1>Tu reserva ha sido $status</h1>
                                    <p>Hola $customerName,</p>
                                    <p>Te informamos que el estado de tu reserva en $restaurantName ha sido actualizado a: <strong>$status</strong>.</p>
                                    <p>Gracias por elegirnos.</p>
                                    <p>Saludos,</p>
                                    <p>El equipo de $restaurantName</p>";

                    $mail->send();
                } catch (Exception $e) {
                    echo "Error al enviar el correo: {$mail->ErrorInfo}";
                }
            }
            
            echo 'success';
        } else {
            echo 'error';
        }

        $stmt->close();
        $conn->close();
    }
?>