<?php
    require_once '../../db_config.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $address = $_POST['address'];
        $phone_number = $_POST['phone_number'];
        $email = $_POST['email'];
        $description = $_POST['description'];
        $open_time = $_POST['open_time'];
        $close_time = $_POST['close_time'];

        $stmt = $conn->prepare("UPDATE restaurant SET name = ?, address = ?, phone_number = ?, email = ?, description = ?, open_time = ?, close_time = ? WHERE id = ?");
        $stmt->bind_param("sssssssi", $name, $address, $phone_number, $email, $description, $open_time, $close_time, $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }
?>