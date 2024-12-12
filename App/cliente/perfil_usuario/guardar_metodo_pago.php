<?php
    // Incluimos la configuración de la base de datos
    require_once '../../db_config.php';

    // Verificamos si la solicitud se ha realizado mediante el método POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtenemos los datos del formulario enviados por POST
        $customer_id = $_POST['customer_id']; // ID del cliente
        $type = $_POST['type']; // Tipo de método de pago
        // Obtenemos los datos opcionales, si están presentes
        $card_number = $_POST['card_number'] ?? null; // Número de tarjeta
        $card_holder_name = $_POST['card_holder_name'] ?? null; // Nombre del titular de la tarjeta
        $expiry_date = $_POST['expiry_date'] ?? null; // Fecha de expiración
        $iban = $_POST['iban'] ?? null; // IBAN para transferencias bancarias
        $paypal_email = $_POST['paypal_email'] ?? null; // Email de PayPal

        // Preparamos la consulta SQL para insertar un nuevo método de pago
        $query = "INSERT INTO PaymentMethod (customer_id, type, card_number, card_holder_name, expiry_date, iban, paypal_email)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query); // Preparamos la declaración
        // Vinculamos los parámetros a la consulta (tipo de datos: i = integer, s = string)
        $stmt->bind_param('issssss', $customer_id, $type, $card_number, $card_holder_name, $expiry_date, $iban, $paypal_email);

        // Ejecutamos la consulta
        if ($stmt->execute()) {
            // Si la ejecución es exitosa, obtenemos el ID del nuevo método de pago insertado
            $id = $stmt->insert_id;
            // Generamos el HTML para mostrar el nuevo método de pago
            echo "
                <div class='card mb-3' id='metodo-pago-$id'>
                    <div class='card-body'>
                        <h5 class='card-title'>$type</h5>
                        <p class='card-text'>
                            " . ($card_number ? "Número de tarjeta: **** **** **** " . substr($card_number, -4) . "<br>" : "") . "
                            " . ($paypal_email ? "PayPal: $paypal_email<br>" : "") . "
                        </p>
                        <button class='btn btn-danger btn-sm eliminar-metodo-pago' data-id='$id'>Eliminar</button>
                    </div>
                </div>";
        }
    }
?>