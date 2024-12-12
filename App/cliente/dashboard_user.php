<?php
    // Conecto a la configuración de la base de datos para poder interactuar con ella
    require_once '../db_config.php';
    // Inicio una sesión para gestionar datos del usuario mientras navega
    session_start();

    // Compruebo si el usuario está autenticado y su rol es 'customer'
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
        // Si no está autenticado o no es un cliente, lo redirijo a la página de login
        header('Location: ../login.php');
        exit(); // Detengo la ejecución del script para evitar que continúe
    }
?>


<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Dashboard Usuario</title>
        <link rel="stylesheet" href="css_menu/style.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <style>
            body {
                display: flex;
                align-items: center;
                flex-direction: column;
                justify-content: center;
                height: 100vh;
                background: url('../img/02.jpg') no-repeat center center fixed;
                background-size: cover;
                color: #FFFFFF;
            }
            .olimpia{
                padding: 5px 10px;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
                background-color: rgb(0, 0, 0);
                text-align: center;
            }
            /* Footer elegante */
            .footer {
                background-color: #2F2F2F;
                width: 100%;
                padding: 15px;
                font-size: 14px;
                color: #ccc;
                position: relative;
                text-align: center;
                z-index: 10;
            }
        </style>
    </head>
    <body>

        <!-- Contenido principal -->
        <div class="content">
            <div class="olimpia">
                <h1>Bienvenido a Olimpia</h1>
                <h3>Tus sito web de reserva para restaurantes</h3>
            </div>
        </div>

        <ul class="menu">
            <div class="menuToggle"><ion-icon name="add-outline"></ion-icon></div>

            <li style="--i: 0; --clr: #D4AF37">
                <a href="perfil_usuario/perfil_usuario.php" data-name="Perfil de usuario">
                    <ion-icon name="person-outline"></ion-icon>
                </a>
            </li>
            <li style="--i: 1; --clr: #D4AF37">
                <a href="restaurantes/restaurantes.php" data-name="Restaurantes">
                    <ion-icon name="restaurant-outline"></ion-icon>
                </a>
            </li>
            <li style="--i: 2; --clr: #D4AF37">
                <a href="usuario_pedidos/usuario_pedidos.php" data-name="Pedidos anticipados">
                    <ion-icon name="cart-outline"></ion-icon>
                </a>
            </li>
            <li style="--i: 3; --clr: #D4AF37">
                <a href="../../logout.php" data-name="Cerrar sesión">
                    <ion-icon name="log-out-outline"></ion-icon>
                </a>
            </li>

        </ul>

        <script
            type="module"
            src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"
        ></script>
        <script
            nomodule
            src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"
        ></script>

        <!-- JavaScript para el toggle del menú -->
        <script>
            $(document).ready(function () {
                // Selecciono el botón de toggle y el menú
                let menuToggle = document.querySelector(".menuToggle");
                let menu = document.querySelector(".menu");

                // Al hacer clic en el botón, alterno la clase "active" del menú
                menuToggle.onclick = function () {
                    menu.classList.toggle("active");

                    if (menu.classList.contains("active")) {
                        // Si el menú está activo, muestro los nombres de los ítems
                        $(".menu li a").each(function () {
                            let name = $(this).data("name");
                            let tooltip = $('<span class="tooltip"></span>').text(name);
                            $(this).append(tooltip);
                        });
                    } else {
                        // Si no, los elimino
                        $(".tooltip").remove();
                    }
                };
            });
        </script>

        <?php require_once '../footer.php'; ?>
    </body>
</html>