<?php require_once 'edit_user_profile.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="././css/styles.css">
</head>
<body>
    <h2>Perfil de Usuario</h2>
    <?php if ($error): ?><p style="color:red;"><?php echo $error; ?></p><?php endif; ?>
    <?php if ($success): ?><p style="color:green;"><?php echo $success; ?></p><?php endif; ?>

    <form action="user_profile.php" method="POST">
        <label>Teléfono:</label>
        <input type="text" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>"><br>
        <label>Dirección:</label>
        <input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>"><br>
        <button type="submit">Guardar Cambios</button>
    </form>
    <?php require_once '../../footer.php'; ?>
</body>
</html>