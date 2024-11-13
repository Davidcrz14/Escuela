<?php
session_start();
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Obtener el ID del instituto
$instituto_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$instituto_id) {
    header("Location: index.php");
    exit();
}

// Obtener información del instituto
$query_instituto = "SELECT * FROM institutos WHERE id = :id";
$stmt_instituto = $db->prepare($query_instituto);
$stmt_instituto->bindParam(':id', $instituto_id);
$stmt_instituto->execute();
$instituto = $stmt_instituto->fetch(PDO::FETCH_ASSOC);

// Obtener noticias del instituto
$query_noticias = "SELECT * FROM instituto_noticias WHERE instituto_id = :instituto_id ORDER BY fecha_creacion DESC";
$stmt_noticias = $db->prepare($query_noticias);
$stmt_noticias->bindParam(':instituto_id', $instituto_id);
$stmt_noticias->execute();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <!-- ... -->
</head>
<body>
    <!-- Similar a index.php pero mostrando solo noticias del instituto -->
</body>
</html>
