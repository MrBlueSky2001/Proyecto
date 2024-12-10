<?php 
    require_once 'edit_perfil_usuario.php'; 

    $success = $_GET['success'] ?? '';
    $error = $_GET['error'] ?? '';

    $customer_id = $user['id'];
    $payment_methods = [];
    $stmt = $conn->prepare("SELECT * FROM PaymentMethod WHERE customer_id = ?");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $payment_methods[] = $row;
    }
    $stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Perfil de Usuario</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <style>
            body {
                background-color: #F8F5F2;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }
            .container {
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }
            h2 {
                color: #000000;
            }
            .alert {
                margin-bottom: 20px;
            }
            .custom-btn {
                background-color: #ffffff;
                border: 2px solid #D4AF37;
                color: black;
                font-size: 18px;
                padding: 10px 25px;
                border-radius: 25px;
                font-weight: 600;
                transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
                cursor: pointer;
                display: inline-block; 
            }
            .custom-btn:hover {
                background-color: #D4AF37;
                border-color: #D4AF37;
                color: white;
            }
            .footer {
                background-color: #2F2F2F;
                width: 100%;
                padding: 15px;
                font-size: 14px;
                color: #ccc;
                text-align: center;
                position: absolute;
                bottom: 0;
                left: 0;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h2>Perfil de Usuario</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form action="perfil_usuario.php" method="POST">
                <div class="mb-3">
                    <label for="phone_number" class="form-label">Teléfono:</label>
                    <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Dirección:</label>
                    <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="text" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <button type="submit" class="custom-btn">Guardar Cambios</button>
            </form>

            <button class="custom-btn" data-bs-toggle="modal" data-bs-target="#anadirMetodoPagoModal">Añadir Método de Pago</button>

            <div id="container-metodo-pago" class="mt-4">
                <?php foreach ($payment_methods as $method): ?>
                    <div class="card mb-3" id="metodo-pago-<?php echo $method['id']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $method['type']; ?></h5>
                            <p class="card-text">
                                <?php echo ($method['card_number'] ? "Número de tarjeta: **** **** **** " . substr($method['card_number'], -4) . "<br>" : ""); ?>
                                <?php echo ($method['paypal_email'] ? "PayPal: " . htmlspecialchars($method['paypal_email']) . "<br>" : ""); ?>
                            </p>
                            <button class="btn btn-danger btn-sm eliminar-metodo-pago" data-id="<?php echo $method['id']; ?>">Eliminar</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Modal para añadir método de pago -->
        <div class="modal fade" id="anadirMetodoPagoModal" tabindex="-1" aria-labelledby="anadirMetodoPagoModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="anadirMetodoPagoModalLabel">Añadir Método de Pago</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="anadirMetodoPagoForm">
                            <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
                            <div class="mb-3">
                                <label for="type" class="form-label">Tipo de Método de Pago</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Tarjeta de Crédito">Tarjeta de Crédito</option>
                                    <option value="Tarjeta de Débito">Tarjeta de Débito</option>
                                    <option value="PayPal">PayPal</option>
                                    <option value="Transferencia Bancaria">Transferencia Bancaria</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                            <div id="detallesMetodo"></div>
                            <button type="submit" class="custom-btn">Guardar Método de Pago</button>
                        </form>
                    </div>
                    <!-- <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div> -->
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            document.getElementById('anadirMetodoPagoForm').addEventListener('submit', function(event) {
                event.preventDefault();
                const formData = new FormData(this);
                fetch('guardar_metodo_pago.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    document.getElementById('container-metodo-pago').insertAdjacentHTML('beforeend', data);
                    document.getElementById('anadirMetodoPagoForm').reset();
                    const modal = bootstrap.Modal.getInstance(document.getElementById('anadirMetodoPagoModal'));
                    modal.hide();
                })
                .catch(error => console.error('Error:', error));
            });

            document.addEventListener('click', function(event) {
                if (event.target.classList.contains('eliminar-metodo-pago')) {
                    const id = event.target.getAttribute('data-id');
                    fetch('eliminar_metodo_pago.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ id: id })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('metodo-pago-' + id).remove();
                        } else {
                            alert(data.message || 'Error al eliminar el método de pago.');
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            });
        </script>
        <script>
            document.getElementById('type').addEventListener('change', function() {
                const detallesMetodo = document.getElementById('detallesMetodo');
                detallesMetodo.innerHTML = '';

                const type = this.value;
                if (type === 'Tarjeta de Crédito' || type === 'Tarjeta de Débito') {
                    detallesMetodo.innerHTML = `
                        <div class="mb-3">
                            <label for="card_number" class="form-label">Número de Tarjeta</label>
                            <input type="text" class="form-control" id="card_number" name="card_number" maxlength="16" required>
                        </div>
                        <div class="mb-3">
                            <label for="card_holder_name" class="form-label">Nombre del Titular</label>
                            <input type="text" class="form-control" id="card_holder_name" name="card_holder_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="expiry_date" class="form-label">Fecha de Expiración (YYYY-MM-DD)</label>
                            <input type="text" class="form-control" id="expiry_date" name="expiry_date" required>
                        </div>
                    `;
                } else if (type === 'Transferencia Bancaria') {
                    detallesMetodo.innerHTML = `
                        <div class="mb-3">
                            <label for="iban" class="form-label">IBAN</label>
                            <input type="text" class="form-control" id="iban" name="iban" required>
                        </div>
                    `;
                } else if (type === 'PayPal') {
                    detallesMetodo.innerHTML = `
                        <div class="mb-3">
                            <label for="paypal_email" class="form-label">Email de PayPal</label>
                            <input type="email" class="form-control" id="paypal_email" name="paypal_email" required>
                        </div>
                    `;
                }
            });
        </script>
    </body>
</html>