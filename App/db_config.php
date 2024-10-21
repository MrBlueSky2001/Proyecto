<?php
    $servername = "localhost"; // Cambia esto si tu servidor es diferente
    $username = "root";   // Cambia esto con tu nombre de usuario de MySQL
    $password = ""; // Cambia esto con tu contraseña de MySQL
    $database = "proyecto";      // Cambia esto al nombre de tu base de datos

    // Crear conexión
    $conn = new mysqli($servername, $username, $password, $database);

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
?>