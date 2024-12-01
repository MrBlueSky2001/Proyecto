<?php
    require_once '../db_config.php';
    session_start();

    // Verificar que el usuario esté autenticado y sea administrador
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        header('Location: ../login.php');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title></title>
        <link rel="stylesheet" href="css_menu/style.css" />
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="js/scripts.js"></script>
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

            .content{
                padding: 5px 10px;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
                background-color: rgb(0, 0, 0);
            }
        </style>
    </head>
    <body>

        <div class="content">
            <h1>Bienvenido a la zona de gestión</h1>
        </div>

        <ul class="menu">
            <div class="menuToggle"><ion-icon name="add-outline"></ion-icon></div>
            <li style="--i: 0; --clr: #3498db">
                <a href="admin_clientes/admin_clientes.php">
                    <ion-icon name="people-outline"></ion-icon>
                    <span class="tooltip">Clientes</span>
                </a>
            </li>
            <li style="--i: 1; --clr: #3498db">
                <a href="admin_restaurantes/admin_restaurantes.php">
                    <ion-icon name="restaurant-outline"></ion-icon>
                    <span class="tooltip">Restaurantes</span>
                </a>
            </li>
            <li style="--i: 2; --clr: #3498db">
                <a href="admin_comidas/admin_comidas.php">
                    <ion-icon name="fast-food-outline"></ion-icon>
                    <span class="tooltip">Gestión de comidas</span>
                </a>
            </li>
            <li style="--i: 3; --clr: #3498db">
                <a href="admin_reservas/admin_reservas.php">
                    <ion-icon name="calendar-outline"></ion-icon>
                    <span class="tooltip">Reservas</span>
                </a>
            </li>
            <li style="--i: 4; --clr: #3498db">
                <a href="admin_pedidos/admin_pedidos.php">
                    <ion-icon name="cart-outline"></ion-icon>
                    <span class="tooltip">Pedidos</span>
                </a>
            </li>
            <li style="--i: 5; --clr: #3498db">
                <a href="../../logout.php">
                    <ion-icon name="log-out-outline"></ion-icon>
                    <span class="tooltip">Cerrar sesión</span>
                </a>
            </li>

        </ul>

        <script>
            $(document).ready(function() {
                let menuToggle = document.querySelector(".menuToggle");
                let menu = document.querySelector(".menu");

                menuToggle.onclick = function () {
                    menu.classList.toggle("active");
                };
            });
        </script>

        <script
            type="module"
            src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"
        ></script>
        <script
            nomodule
            src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"
        ></script>
    </body>
</html>