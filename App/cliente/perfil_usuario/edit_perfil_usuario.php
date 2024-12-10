<?php
    session_start();
    require_once '../../db_config.php';

    if (!isset($_SESSION['user'])) {
        header("Location: ../../login.php");
        exit();
    }

    $user = $_SESSION['user'];
    $error = '';
    $success = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $phone_number = htmlspecialchars($_POST['phone_number']);
        $address = htmlspecialchars($_POST['address']);
        $email = htmlspecialchars($_POST['email']);

        $stmt = $conn->prepare("UPDATE customer SET phone_number = ?, address = ?, email = ? WHERE id = ?");
        $stmt->bind_param("sssi", $phone_number, $address, $email, $user['id']);

        if ($stmt->execute()) {
            $success = "Perfil actualizado con éxito.";
            // Actualiza los datos del usuario en la sesión
            $user['phone_number'] = $phone_number;
            $user['address'] = $address;
            $user['email'] = $email;
            $_SESSION['user'] = $user;

            // Redirige para evitar el reenvío del formulario
            header("Location: perfil_usuario.php?success=" . urlencode($success));
            exit();
        } else {
            $error = "Error al actualizar el perfil.";
        }
        $stmt->close();
    }
?>