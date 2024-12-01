<?php
    require_once 'db_config.php';

    $error = '';
    $success = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $dni = $_POST['dni'];

        $stmt = $conn->prepare('SELECT * FROM customer WHERE username = ? AND dni = ?');
        $stmt->bind_param("ss", $username, $dni);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $success = 'Usuario verificado. Por favor, introduce tu nueva contraseña.';
        } else {
            $error = 'El nombre de usuario y DNI no coinciden.';
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Recuperar contraseña</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/styles.css">
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
        <style>
            body {
                display: flex;
                flex-direction: column;
                min-height: 100vh;
                background-color: #F8F5F2; /* Fondo Blanco Marfil */
                font-family: 'Open Sans', sans-serif;
            }
            .container-fluid {
                flex: 1; /* Permite que el contenedor principal ocupe todo el espacio disponible */
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .recover-form {
                background-color: #FFFFFF; /* Fondo Blanco */
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                width: 100%;
                max-width: 400px;
            }
            .recover-form input {
                margin-bottom: 15px;
            }
            .recover-btn {
                background-color: #000000; /* Fondo Negro */
                color: #D4AF37; /* Texto Dorado */
                border: none;
                border-radius: 5px;
                padding: 10px;
                transition: background-color 0.3s, color 0.3s;
            }
            .recover-btn:hover {
                background-color: #D4AF37; /* Fondo Dorado al pasar el mouse */
                color: #000000; /* Texto Negro */
            }
            .logo {
                position: absolute;
                top: 10px;
                left: 10px;
            }
            .logo img {
                width: 100px;
            }
            h2 {
                font-family: 'Playfair Display', serif; /* Fuente para encabezados */
                color: #000000; /* Texto Negro */
            }
        </style>
    </head>
    <body>
        <div class="container-fluid">
            <div class="logo">
                <a href="home.php">
                    <img src="img/logo.jpg" alt="Logo">
                </a>
            </div>

            <div class="recover-form text-center">
                <h2>Recuperar Contraseña</h2>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                    <!-- Formulario para actualizar contraseña -->
                    <form action="act_password.php" method="POST">
                        <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">
                        <div class="form-group">
                            <label for="new_password">Nueva contraseña</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <button type="submit" class="btn recover-btn btn-block">Actualizar contraseña</button>
                    </form>
                <?php else: ?>
                    <form action="recuperar_password.php" method="POST">
                        <div class="form-group">
                            <label for="username">Nombre de usuario</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="dni">DNI</label>
                            <input type="text" class="form-control" id="dni" name="dni" required>
                        </div>
                        <button type="submit" class="btn recover-btn btn-block">Verificar</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <?php require_once 'footer.php'; ?>
    </body>
</html>