<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['pertenece'] !== 'Instituto') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();

    $titulo = $_POST['titulo'];
    $informacion = $_POST['informacion'];
    $prioridad = $_POST['prioridad'];
    $instituto_id = $_SESSION['instituto_id'];
    $creado_por = $_SESSION['username'];

    // Manejar la imagen principal
    $imagen_principal = '';
    if (isset($_FILES['imagen_principal']) && $_FILES['imagen_principal']['error'] === UPLOAD_ERR_OK) {
        $extension = pathinfo($_FILES['imagen_principal']['name'], PATHINFO_EXTENSION);
        $imagen_principal = '../../images/' . uniqid() . '.' . $extension;
        move_uploaded_file($_FILES['imagen_principal']['tmp_name'], $imagen_principal);
    }

    // Manejar las imágenes adicionales
    $imagenes = [];
    if (isset($_FILES['imagenes'])) {
        foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['imagenes']['error'][$key] === UPLOAD_ERR_OK) {
                $extension = pathinfo($_FILES['imagenes']['name'][$key], PATHINFO_EXTENSION);
                $filename = '../../images/' . uniqid() . '.' . $extension;
                move_uploaded_file($tmp_name, $filename);
                $imagenes[] = $filename;
            }
        }
    }
    $imagenes = implode(',', $imagenes);

    // Insertar en instituto_noticias en lugar de rectoria_noticias
    $query = "INSERT INTO instituto_noticias (instituto_id, titulo, informacion, imagen_principal, imagenes, documentos, prioridad, creado_por)
              VALUES (:instituto_id, :titulo, :informacion, :imagen_principal, :imagenes, :documentos, :prioridad, :creado_por)";

    $stmt = $db->prepare($query);
    // Bind parameters...
    $stmt->execute();

    header("Location: dashboard.php");
    exit();
}
?>
