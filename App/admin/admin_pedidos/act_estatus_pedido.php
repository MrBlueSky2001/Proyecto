<?php
    require_once '../../db_config.php';

    // Incluimos el autoload de Composer para cargar las dependencias de PHPMailer
    require '../../vendor/autoload.php';

    // Usamos las clases necesarias de PHPMailer
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id']; // ID del pedido anticipado
        $status = $_POST['status']; // Nuevo estado

        // Preparamos la consulta para actualizar el estado del pedido anticipado
        $stmt = $conn->prepare("UPDATE preorder SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);

        if ($stmt->execute()) {
            // Si el estado es 'confirmado' o 'cancelado', enviamos un correo al cliente
            if ($status === 'confirmado' || $status === 'cancelado') {
                // Realizamos una consulta para obtener el correo y nombre del cliente
                $result = $conn->query("SELECT customer.email, customer.username, reservation.id AS reservation_id 
                                        FROM preorder 
                                        JOIN customer ON preorder.customer_id = customer.id 
                                        JOIN reservation ON preorder.reservation_id = reservation.id
                                        WHERE preorder.id = $id");
                $row = $result->fetch_assoc();
                $customerEmail = $row['email'];  // Correo del cliente
                $customerName = $row['username'];  // Nombre del cliente
                $reservationId = $row['reservation_id'];  // ID de la reserva

                // Creamos una instancia de PHPMailer para enviar el correo
                $mail = new PHPMailer(true);

                try {
                    // Configuración del servidor SMTP de Gmail para enviar el correo
                    $mail->isSMTP();  // Usar SMTP para el envío
                    $mail->Host = 'smtp.gmail.com';  // Servidor SMTP de Gmail
                    $mail->SMTPAuth = true;  // Habilitar autenticación SMTP
                    $mail->Username = 'smatisa951@iesmartinezm.es';  // Usuario de correo (email)
                    $mail->Password = 'MrBlueSky';  // Contraseña del correo
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Seguridad de la conexión
                    $mail->Port = 587;  // Puerto SMTP

                    // Establecemos el remitente del correo y la dirección del cliente
                    $mail->setFrom('smatisa951@iesmartinezm.es', 'Pedidos Restaurante');
                    $mail->addAddress($customerEmail, $customerName);  // Añadir al destinatario

                    // Configuramos el cuerpo del mensaje en formato HTML
                    $mail->isHTML(true);  // Habilitamos el formato HTML
                    $mail->Subject = "Estado de tu pedido anticipado";  // Asunto del correo

                    // Cuerpo del correo
                    if ($status === 'confirmado') {
                        $mail->Body = "<h1>Tu pedido ha sido confirmado</h1>
                                    <p>Hola $customerName,</p>
                                    <p>Te informamos que tu pedido anticipado ha sido confirmado con éxito.</p>
                                    <p>Gracias por elegirnos.</p>
                                    <p>Saludos.</p>";
                    } elseif ($status === 'cancelado') {
                        $mail->Body = "<h1>Lo sentimos, tu pedido ha sido cancelado</h1>
                                    <p>Hola $customerName,</p>
                                    <p>Te informamos que lamentablemente no queda stock de los platos que pediste, por lo que tu pedido ha sido cancelado. No se le aplicarán cargos a su método de pago</p>
                                    <p>Gracias por tu comprensión.</p>
                                    <p>Saludos.</p>";
                    }

                    // Intentamos enviar el correo
                    $mail->send();
                } catch (Exception $e) {
                    // Si ocurre un error al enviar el correo, mostramos el error
                    echo "Error al enviar el correo: {$mail->ErrorInfo}";
                }
            }

            // Si todo ha ido bien, devolvemos 'success'
            echo 'success';
        } else {
            // Si ocurrió un error en la ejecución de la actualización, devolvemos 'error'
            echo 'error';
        }

        // Cerramos la declaración preparada y la conexión a la base de datos
        $stmt->close();
        $conn->close();
    }
?>