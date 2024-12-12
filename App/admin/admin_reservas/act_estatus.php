<?php
    require_once '../../db_config.php';
    
    // Incluimos el autoload de Composer para cargar las dependencias de PHPMailer
    require '../../vendor/autoload.php';

    // Usamos las clases necesarias de PHPMailer
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    // Verificamos si la solicitud es de tipo POST (es decir, si hay datos enviados desde un formulario)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtenemos el ID de la reserva y el nuevo estado desde la solicitud POST
        $id = $_POST['id'];
        $status = $_POST['status'];

        // Actualizamos el estado de la reserva en la base de datos
        // Usamos una sentencia preparada para evitar inyecciones SQL
        $stmt = $conn->prepare("UPDATE reservation SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id); // Vinculamos los parámetros: 's' para el estado (string) y 'i' para el id (integer)

        // Ejecutamos la consulta de actualización
        if ($stmt->execute()) {
            // Si el estado es 'confirmado' o 'cancelado', enviamos un correo al cliente
            if ($status === 'confirmado' || $status === 'cancelado') {

                // Realizamos una consulta para obtener el correo y nombre del cliente y el nombre del restaurante
                $result = $conn->query("SELECT customer.email, customer.username, restaurant.name 
                                        FROM reservation 
                                        JOIN customer ON reservation.customer_id = customer.id 
                                        JOIN restaurant ON reservation.restaurant_id = restaurant.id
                                        WHERE reservation.id = $id");
                // Extraemos los resultados de la consulta
                $row = $result->fetch_assoc();
                $customerEmail = $row['email'];  // Correo del cliente
                $customerName = $row['username'];  // Nombre del cliente
                $restaurantName = $row['name'];  // Nombre del restaurante

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
                    $mail->setFrom('smatisa951@iesmartinezm.es', 'Reserva Restaurant');
                    $mail->addAddress($customerEmail, $customerName);  // Añadir al destinatario

                    // Configuramos el cuerpo del mensaje en formato HTML
                    $mail->isHTML(true);  // Habilitamos el formato HTML
                    $mail->Subject = "Estado de tu reserva en $restaurantName";  // Asunto del correo
                    // Cuerpo del correo, donde informamos al cliente sobre el cambio de estado de su reserva
                    $mail->Body    = "<h1>Tu reserva ha sido $status</h1>
                                    <p>Hola $customerName,</p>
                                    <p>Te informamos que el estado de tu reserva en $restaurantName ha sido actualizado a: <strong>$status</strong>.</p>
                                    <p>Gracias por elegirnos.</p>
                                    <p>Saludos,</p>
                                    <p>El equipo de $restaurantName</p>";

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