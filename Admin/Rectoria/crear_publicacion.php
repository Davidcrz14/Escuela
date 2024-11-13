<?php
session_start();
require_once '../config/database.php';

// Verificar si el usuario ha iniciado sesión y pertenece a Rectoría
if (!isset($_SESSION['user_id']) || $_SESSION['pertenece'] !== 'Rectoria') {
    header("Location: ./index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();

    $titulo = $_POST['titulo'];
    $informacion = $_POST['informacion'];
    $prioridad = $_POST['prioridad'];
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

    // Manejar los documentos
    $documentos = [];
    if (isset($_FILES['documentos'])) {
        foreach ($_FILES['documentos']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['documentos']['error'][$key] === UPLOAD_ERR_OK) {
                $filename = 'docs/' . uniqid() . '_' . $_FILES['documentos']['name'][$key];
                move_uploaded_file($tmp_name, '../' . $filename);
                $documentos[] = $filename;
            }
        }
    }
    $documentos = implode(',', $documentos);

    // Insertar la nueva publicación en la base de datos
    $query = "INSERT INTO rectoria_noticias (titulo, informacion, imagen_principal, imagenes, documentos, prioridad, creado_por)
              VALUES (:titulo, :informacion, :imagen_principal, :imagenes, :documentos, :prioridad, :creado_por)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':informacion', $informacion);
    $stmt->bindParam(':imagen_principal', $imagen_principal);
    $stmt->bindParam(':imagenes', $imagenes);
    $stmt->bindParam(':documentos', $documentos);
    $stmt->bindParam(':prioridad', $prioridad);
    $stmt->bindParam(':creado_por', $creado_por);
    $stmt->execute();

    header("Location: dashboard.php");
    exit();
}
?>
