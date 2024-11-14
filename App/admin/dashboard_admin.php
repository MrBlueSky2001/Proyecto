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
</head>
<body>
    <ul class="menu">
        <div class="menuToggle"><ion-icon name="add-outline"></ion-icon></div>
        <li style="--i: 0; --clr: #3498db">
            <a href="admin_customers/admin_customers.php"><ion-icon name="people-outline"></ion-icon></a>
        </li>
        <li style="--i: 1; --clr: #3498db">
            <a href="admin_restaurants/admin_restaurants.php"><ion-icon name="restaurant-outline"></ion-icon></a>
        </li>
        <li style="--i: 2; --clr: #3498db">
            <a href="admin_foods/admin_foods.php"><ion-icon name="fast-food-outline"></ion-icon></a>
        </li>
        <li style="--i: 3; --clr: #3498db">
            <a href="admin_reservations.php"><ion-icon name="calendar-outline"></ion-icon></a>
        </li>
        <li style="--i: 4; --clr: #3498db">
            <a href="admin_preorders.php"><ion-icon name="cart-outline"></ion-icon></a>
        </li>
        <li style="--i: 5; --clr: #3498db">
            <a href="../../logout.php"><ion-icon name="log-out-outline"></ion-icon></a>
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
<?php require_once '../footer.php';?>
</html>