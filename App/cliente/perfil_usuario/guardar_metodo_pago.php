<?php
    require_once '../../db_config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $customer_id = $_POST['customer_id'];
        $type = $_POST['type'];
        $card_number = $_POST['card_number'] ?? null;
        $card_holder_name = $_POST['card_holder_name'] ?? null;
        $expiry_date = $_POST['expiry_date'] ?? null;
        $iban = $_POST['iban'] ?? null;
        $paypal_email = $_POST['paypal_email'] ?? null;

        $query = "INSERT INTO PaymentMethod (customer_id, type, card_number, card_holder_name, expiry_date, iban, paypal_email)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('issssss', $customer_id, $type, $card_number, $card_holder_name, $expiry_date, $iban, $paypal_email);

        if ($stmt->execute()) {
            $id = $stmt->insert_id;
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