<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Por favor, complete todos los campos";
        header("Location: ../index.php");
        exit();
    }

    try {
        $query = "SELECT * FROM alumnos WHERE username = :username";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($password === $row['password']) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['login_attempts'] = 0;

                header("Location: ../dashboard/index.php");
                exit();
            } else {
                $_SESSION['error'] = "Contraseña incorrecta";
                $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;

                if ($_SESSION['login_attempts'] >= 3) {
                    $ip_address = $_SERVER['REMOTE_ADDR'];
                    $block_time = time() + (60 * 5); // Bloquear por 5 minutos

                    $query = "INSERT INTO blocked_ips (ip_address, block_time) VALUES (:ip_address, :block_time)";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(":ip_address", $ip_address);
                    $stmt->bindParam(":block_time", $block_time);
                    $stmt->execute();

                    $_SESSION['error'] = "Demasiados intentos fallidos. Su IP ha sido bloqueada temporalmente";
                    unset($_SESSION['login_attempts']);
                }
            }
        } else {
            $_SESSION['error'] = "Usuario no encontrado";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error en el sistema. Por favor, intente más tarde";
    }

    header("Location: ../index.php");
    exit();
}
?>
