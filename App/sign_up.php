<?php
    require_once 'db_config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $phone_number = $_POST['phone_number'];
        $address = $_POST['address'];
        $dni = $_POST['dni'];

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO customer (username, password, phone_number, address, dni) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $hashed_password, $phone_number, $address, $dni);

        if ($stmt->execute()) {
            header('Location: cliente/dashboard_user.php');
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
            .login-form input, .login-form textarea {
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

            <div class="login-form">
                <h2>Registro</h2>
                <form action="sign_up.php" method="POST" onsubmit="validarFormulario(event)">
                    <input type="text" name="username" placeholder="Nombre de usuario" required class="form-control">
                    <input type="password" name="password" placeholder="Contraseña" required class="form-control">
                    <input type=" text" name="phone_number" placeholder="Número de teléfono" required class="form-control">
                    <textarea name="address" placeholder="Dirección" required class="form-control"></textarea>
                    <input type="text" id="dni" name="dni" placeholder="DNI" required class="form-control">
                    <button type="submit" class="btn login-btn btn-block">Registrarse</button>
                </form>
                <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
            </div>
        </div>

        <?php require_once 'footer.php'; ?>

        <script>
            // Función para validar el DNI
            function validarDNI(dni) {
                const letras = "TRWAGMYFPDXBNJZSQVHLCKE";
                const dniRegex = /^[0-9]{8}[A-Z]$/;

                if (!dniRegex.test(dni)) {
                    return false; // Formato inválido
                }

                const numero = parseInt(dni.slice(0, 8), 10); // Extrae los números
                const letra = dni[8]; // Extrae la letra
                const letraCorrecta = letras[numero % 23]; // Calcula la letra correcta

                return letra === letraCorrecta; // Compara la letra ingresada con la correcta
            }

            // Validación del formulario antes de enviarlo
            function validarFormulario(event) {
                const dni = document.getElementById("dni").value;

                if (!validarDNI(dni)) {
                    event.preventDefault(); // Evita el envío del formulario
                    alert("El DNI ingresado no es válido. Por favor, verifica los datos.");
                }
            }
        </script>
    </body>
</html>