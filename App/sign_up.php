<?php
    require_once 'db_config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $phone_number = $_POST['phone_number'];
        $address = $_POST['address'];

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO customer (username, password, phone_number, address) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $hashed_password, $phone_number, $address);

        if ($stmt->execute()) {
            header('Location: dashboard_user.php');
            exit();
        } else {
            echo "Error al registrarse: " . $stmt->error;
        }

        $stmt->close();
    }
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registro</title>
        <link rel="stylesheet" href="path/to/style.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
        <div class="login-container">
            <img src="img/logo.jpg" alt="Logo" class="logo">
            
            <div class="login-form">
                <h2>Registro</h2>
                <form action="sign_up.php" method="POST">
                    <input type="text" name="username" placeholder="Nombre de usuario" required class="form-control">
                    <input type="password" name="password" placeholder="Contraseña" required class="form-control">
                    <input type="text" name="phone_number" placeholder="Número de teléfono" required class="form-control">
                    <textarea name="address" placeholder="Dirección" required class="form-control"></textarea>
                    
                    <button type="submit" class="custom-btn">Registrarse</button>
                </form>
                <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
            </div>
        </div>

        <?php require_once 'footer.php'; ?>
    </body>
</html>
