<?php
    require_once 'db_config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

        $stmt = $conn->prepare('UPDATE customer SET password = ? WHERE username = ?');
        $stmt->bind_param("ss", $new_password, $username);

        if ($stmt->execute()) {
            echo "<script>alert('Contraseña actualizada con éxito.'); window.location.href = 'login.php';</script>";
        } else {
            echo "Error al actualizar la contraseña: " . $stmt->error;
        }
    }
?>