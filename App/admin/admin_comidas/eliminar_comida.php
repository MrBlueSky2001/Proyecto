<?php
    require_once '../../db_config.php';

    // Iniciamos la sesión para poder acceder a los datos de la sesión, como el usuario logueado
    session_start();

    // Establecemos el tipo de contenido que se devolverá: JSON. Esto es útil para las respuestas AJAX.
    header('Content-Type: application/json');

    // Verificamos que el usuario esté autenticado y que tenga el rol de 'admin'. Si no cumple con estas condiciones,
    // lo redirigimos al usuario a la página de login, ya que no está autorizado para realizar esta acción.
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        header('Location: ../../login.php');
        exit(); // Terminamos la ejecución del script
    }

    // Obtenemos el ID de la comida que queremos eliminar, enviado como parámetro GET.
    $id = $_GET['id']; // El ID se recibe como un parámetro en la URL (por ejemplo: delete.php?id=123)

    // Verificamos si el ID fue proporcionado. Si no se proporciona, devolvemos un mensaje de error en formato JSON.
    if (empty($id)) {
        // Si el ID está vacío, enviamos un mensaje de error al cliente en formato JSON.
        echo json_encode(['error' => 'ID no proporcionado']);
        exit(); // Terminamos la ejecución si no se proporciona el ID
    }

    // Preparamos la consulta SQL para eliminar la comida de la base de datos, basándose en el ID.
    $stmt = $conn->prepare("DELETE FROM food WHERE id = ?");

    // Asociamos el parámetro del ID (de tipo entero) a la consulta SQL.
    $stmt->bind_param('i', $id); // 'i' indica que el parámetro es un entero (integer)

    // Ejecutamos la consulta SQL. Si la eliminación tuvo éxito, devolvemos un mensaje de éxito.
    if ($stmt->execute()) {
        // Si la comida se eliminó correctamente, devolvemos un mensaje de éxito en formato JSON.
        echo json_encode(['success' => 'Comida eliminada con éxito']);
    } else {
        // Si hubo un error al ejecutar la consulta, devolvemos un mensaje de error en formato JSON.
        echo json_encode(['error' => 'Error al eliminar la comida']);
    }

    // Cerramos la sentencia preparada para liberar recursos.
    $stmt->close();

    // Cerramos la conexión a la base de datos después de realizar la operación.
    $conn->close();
?>