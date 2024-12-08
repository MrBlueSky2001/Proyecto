<?php
    require_once 'db_config.php';
    session_start();

    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $stmt = $conn->prepare('SELECT * FROM customer WHERE username = ?');
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;

            if ($user['role'] === 'admin') {
                header('Location: admin/dashboard_admin.php');
            } elseif ($user['role'] === 'customer') {
                header('Location: cliente/dashboard_user.php');
            }
            exit();
        } else {
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
                background-color: #F8F5F2; /* Fondo Blanco Marfil */
                font-family: 'Open Sans', sans-serif;
            }
            .container-fluid {
                flex: 1; /* Permite que el contenedor principal ocupe todo el espacio disponible */
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .login-form {
                background-color: #FFFFFF; /* Fondo Blanco */
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
                background-color: #000000; /* Fondo Negro */
                color: #D4AF37; /* Texto Dorado */
                border: none;
                border-radius: 5px;
                padding: 10px;
                transition: background-color 0.3s, color 0.3s;
            }
            .login-btn:hover {
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