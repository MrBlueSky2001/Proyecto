<?php
    require_once 'db_config.php';

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
            if ($user['role'] === 'admin') {
                header('Location: dashboard_admin.php');
            } else {
                header('Location: dashboard_user.php');
            }
            exit();
        } else {
            $error = 'Usuario o contraseña incorrectos';
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - Proyecto</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/styles.css">
        <style>
            body {
                background-color: #f7f7f7;
            }
            .login-container {
                height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .login-form {
                background-color: #fff;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                width: 100%;
                max-width: 400px;
            }
            .login-form input {
                margin-bottom: 15px;
            }
            .login-btn {
                background-color: #D4AF37;
                border: 1px solid #D4AF37;
            }
            .login-btn:hover {
                background-color: #b99a2f;
            }
            .logo {
                position: absolute;
                top: 10px;
                left: 10px;
            }
            .logo img {
                width: 100px;
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

            <div class="login-container">
                <div class="login-form col-md-4 col-sm-8 col-12 text-center">
                    <h2 class="mb-4">Iniciar Sesión</h2>

                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="POST">
                        <input type="text" class="form-control" name="username" placeholder="Nombre de usuario" required>
                        <input type="password" class="form-control" name="password" placeholder="Contraseña" required>
                        <button type="submit" class="btn login-btn btn-block">Iniciar Sesión</button>
                    </form>

                    <p class="mt-3">
                        <a href="sign_up.php">Regístrate si no tienes cuenta</a>
                    </p>
                </div>
            </div>
        </div>
        <?php require_once 'footer.php'; ?>
    </body>
</html>
