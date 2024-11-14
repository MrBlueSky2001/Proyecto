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
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Usuario</title>
    <link rel="stylesheet" href="css_menu/style.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <ul class="menu">
        <div class="menuToggle"><ion-icon name="add-outline"></ion-icon></div>
        <li style="--i: 0; --clr: #D4AF37">
            <a href="user_profile/user_profile.php"><ion-icon name="person-outline"></ion-icon></a>
        </li>
        <li style="--i: 1; --clr: #D4AF37">
            <a href="restaurants.php"><ion-icon name="restaurant-outline"></ion-icon></a>
        </li>
        <li style="--i: 2; --clr: #D4AF37">
            <a href="user_reservations.php"><ion-icon name="calendar-outline"></ion-icon></a>
        </li>
        <li style="--i: 3; --clr: #D4AF37">
            <a href="user_preorders.php"><ion-icon name="cart-outline"></ion-icon></a>
        </li>
        <li style="--i: 4; --clr: #D4AF37">
            <a href="../../logout.php"><ion-icon name="log-out-outline"></ion-icon></a>
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
        $(document).ready(function() {
            let menuToggle = document.querySelector(".menuToggle");
            let menu = document.querySelector(".menu");

            menuToggle.onclick = function () {
                menu.classList.toggle("active");
            };
        });
    </script>
</body>
<?php require_once '../footer.php';?>
</html>