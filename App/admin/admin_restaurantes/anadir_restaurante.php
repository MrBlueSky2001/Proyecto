<?php
    require_once '../../db_config.php';
    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        header('Location: ../../login.php');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $address = $_POST['address'];
        $phone_number = $_POST['phone_number'];
        $email = $_POST['email'];
        $description = $_POST['description'];
        $open_time = $_POST['open_time'];
        $close_time = $_POST['close_time'];

        $stmt = $conn->prepare("INSERT INTO restaurant (name, address, phone_number, email, description, open_time, close_time) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $name, $address, $phone_number, $email, $description, $open_time, $close_time);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
    }
?>