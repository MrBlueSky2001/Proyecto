<?php
    require_once '../db_config.php';
    session_start();

    // Verificar que el usuario esté autenticado y sea un usuario
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
        header('Location: ../login.php');
        exit();
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
            }
            /* Footer elegante */
            .footer {
                background-color: #2F2F2F;
                width: 100%;
                padding: 15px;
                font-size: 14px;
                color: #ccc; /* Texto en color gris claro */
                position: relative; /* Cambiado a relative para evitar superposición */
                text-align: center;
                z-index: 10; /* Asegura que esté por encima de otros elementos */
            }
        </style>
    </head>
    <body>

        <!-- Contenido principal -->
        <div class="content">
            <div class="olimpia">
                <h1>Bienvenido a Olimpia</h1>
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

        <!-- Ionicons para los iconos del menú -->
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
                let menuToggle = document.querySelector(".menuToggle");
                let menu = document.querySelector(".menu");

                menuToggle.onclick = function () {
                    menu.classList.toggle("active");

                    if (menu.classList.contains("active")) {
                        // Mostrar los nombres
                        $(".menu li a").each(function () {
                            let name = $(this).data("name");
                            let tooltip = $('<span class="tooltip"></span>').text(name);
                            $(this).append(tooltip);
                        });
                    } else {
                        // Ocultar los nombres
                        $(".tooltip").remove();
                    }
                };
            });
        </script>

        <?php require_once '../footer.php'; ?>
    </body>
</html>