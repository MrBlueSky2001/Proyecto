<?php 
    require_once 'edit_perfil_usuario.php'; 

    $success = $_GET['success'] ?? '';
    $error = $_GET['error'] ?? '';
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
                background-color: #F8F5F2; /* Fondo de la página */
                min-height: 100vh; /* Asegura que el body tenga una altura mínima de 100vh */
                display: flex; /* Asegura que el body se comporte como un contenedor flexible */
                flex-direction: column; /* Asegura que el contenido se coloque en una columna */
            }
            .container {
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }
            h2 {
                color: #000000; /* Texto negro */
            }
            .alert {
                margin-bottom: 20px;
            }
            .custom-btn {
                background-color: #ffffff; /* Color blanco para el botón */
                border: 2px solid #D4AF37; /* Borde color dorado */
                color: black;
                font-size: 18px;
                padding: 10px 25px;
                border-radius: 25px;
                font-weight: 600;
                transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease; /* Añadido color en la transición */
                cursor: pointer; /* Cambia el cursor al pasar sobre el botón */
                display: inline-block; /* Asegura que el botón se comporte como un bloque */
            }
            .custom-btn:hover {
                background-color: #D4AF37; /* Color dorado al hacer hover */
                border-color: #D4AF37; /* Color dorado más vivo al hacer hover */
                color: white; /* Cambia el color del texto al blanco */
            }
            .footer {
                background-color: #2F2F2F;
                width: 100%;
                padding: 15px;
                font-size: 14px;
                color: #ccc; /* Texto en color gris claro */
                text-align: center; /* Centra el texto del footer */
                position: absolute; /* Posiciona el footer al pie de la página */
                bottom: 0; /* Asegura que el footer esté al pie de la página */
                left: 0; /* Asegura que el footer esté alineado a la izquierda */
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
                <button type="submit" class="custom-btn">Guardar Cambios</button>
            </form>
        </div>

        <?php require_once '../../footer.php'; ?>
    </body>
</html>