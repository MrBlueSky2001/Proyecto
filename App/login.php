<?php
    // Incluimos el archivo de configuración para la base de datos
    require_once 'db_config.php';

    // Iniciamos la sesión para poder gestionar los datos del usuario
    session_start();

    // Variable para almacenar errores que puedan surgir durante el inicio de sesión
    $error = '';

    // Comprobamos si la petición es de tipo POST (cuando el usuario envía el formulario)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recogemos los datos enviados desde el formulario de inicio de sesión
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Preparamos una consulta para buscar al usuario en la base de datos
        $stmt = $conn->prepare('SELECT * FROM customer WHERE username = ?');
        // Vinculamos el nombre de usuario a la consulta para evitar inyección SQL
        $stmt->bind_param("s", $username);
        // Ejecutamos la consulta
        $stmt->execute();
        // Obtenemos el resultado de la consulta
        $result = $stmt->get_result();

        // Obtenemos los datos del usuario (si existe) como un array asociativo
        $user = $result->fetch_assoc();

        // Comprobamos si el usuario existe y si la contraseña proporcionada es correcta
        if ($user && password_verify($password, $user['password'])) {
            // Guardamos los datos del usuario en la sesión para que persistan mientras esté conectado
            $_SESSION['user'] = $user;

            // Redirigimos al usuario según su rol: administrador o cliente
            if ($user['role'] === 'admin') {
                header('Location: admin/dashboard_admin.php'); // Panel de administrador
            } elseif ($user['role'] === 'customer') {
                header('Location: cliente/dashboard_user.php'); // Panel de cliente
            }
            exit(); // Finalizamos el script para evitar que se ejecute código adicional
        } else {
            // Si las credenciales no son válidas, mostramos un mensaje de error
            $error = 'Usuario o contraseña incorrectos.';
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - Proyecto</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/styles.css">
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
        <style>
            body {
                display: flex;
                flex-direction: column;
                min-height: 100vh;
                background-color: #F8F5F2;
                font-family: 'Open Sans', sans-serif;
            }
            .container-fluid {
                flex: 1;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .login-form {
                background-color: #FFFFFF; 
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                width: 100%;
                max-width: 400px;
            }
            .login-form input {
                margin-bottom: 15px;
            }
            .login-btn {
                background-color: #000000;
                color: #D4AF37;
                border: none;
                border-radius: 5px;
                padding: 10px;
                transition: background-color 0.3s, color 0.3s;
            }
            .login-btn:hover {
                background-color: #D4AF37;
                color: #000000;
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
                font-family: 'Playfair Display', serif; 
                color: #000000; 
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

            <div class="login-form text-center">
                <h2 class="mb-4">Iniciar Sesión</h2>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="POST">
                    <input type="text" name="username" placeholder="Nombre de usuario" required class="form-control">
                    <input type="password" name="password" placeholder="Contraseña" required class="form-control">
                    <button type="submit" class="btn login-btn btn-block">Iniciar Sesión</button>
                </form>
                <p>¿No tienes una cuenta? <a href="sign_up.php">Regístrate aquí</a></p>
                <p>¿Has olvidado tu contraseña? <a href="recuperar_password.php">Restablece aquí</a></p>
            </div>
        </div>

        <?php require_once 'footer.php'; ?>
    </body>
</html>