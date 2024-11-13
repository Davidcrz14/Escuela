<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

$noticia_id = $_GET['id'];

// Obtener los detalles de la noticia
$query = "SELECT * FROM rectoria_noticias WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $noticia_id);
$stmt->execute();
$noticia = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$noticia) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($noticia['titulo']); ?> - Universidad</title>
    <link rel="icon" href="logo.jpg" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/noticia.css">
</head>
<body>
    <nav class="navbar text-white py-4">
        <div class="container mx-auto flex items-center justify-between">
            <div class="flex items-center">
                <img src="logo.jpg" alt="" class="w-12 h-12 rounded-full mr-4">
                <a class="text-xl font-bold" href="index.php">
                    <i class="fas fa-university me-2"></i>Universidad Tecnologica de la Mixteca
                </a>
            </div>
            <div class="flex items-center">
                <a href="editar_perfil.php" class="text-white hover:underline mr-4">
                    <i class="fas fa-user-edit me-2"></i>Editar Perfil
                </a>
                <a class="text-white hover:underline" href="../auth/logout.php">
                    <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                </a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto mt-8">
        <a href="index.php" class="btn-regresar">
            <i class="fas fa-arrow-left"></i>
            Regresar al Inicio
        </a>

        <div class="noticia-container">
            <h2 class="noticia-titulo"><?php echo htmlspecialchars($noticia['titulo']); ?></h2>

            <div class="noticia-meta">
                <div class="noticia-meta-item">
                    <i class="fas fa-user"></i>
                    <span>Publicado por: <?php echo htmlspecialchars($noticia['creado_por']); ?></span>
                </div>
                <div class="noticia-meta-item">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Fecha: <?php echo date('d/m/Y', strtotime($noticia['fecha_creacion'])); ?></span>
                </div>
            </div>

            <div class="portada-container">
                <img src="../<?php echo htmlspecialchars($noticia['imagen_principal']); ?>"
                     alt="<?php echo htmlspecialchars($noticia['titulo']); ?>"
                     class="portada-img">
            </div>

            <div class="noticia-contenido">
                <h3 class="text-center font-bold text-2xl mb-4"><i class="fas fa-info-circle mr-2"></i>Descripción</h3>
                <div class="text-center"><?php echo nl2br(htmlspecialchars($noticia['informacion'])); ?></div>
            </div>

            <?php if (!empty($noticia['imagenes'])) { ?>
                <div class="noticia-seccion">
                    <h3 class="noticia-seccion-titulo">
                        <i class="fas fa-images"></i>
                        Galería de Imágenes
                    </h3>
                    <div class="grid">
                        <?php
                        $imagenes = explode(',', $noticia['imagenes']);
                        foreach ($imagenes as $imagen) {
                        ?>
                            <img src="../<?php echo htmlspecialchars($imagen); ?>"
                                 alt="Imagen adicional"
                                 class="w-full">
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>

            <?php if (!empty($noticia['documentos'])) { ?>
                <div class="noticia-seccion">
                    <h3 class="noticia-seccion-titulo">
                        <i class="fas fa-file-alt"></i>
                        Documentos Adjuntos
                    </h3>
                    <div class="grid">
                        <?php
                        $documentos = explode(',', $noticia['documentos']);
                        foreach ($documentos as $documento) {
                        ?>
                            <a href="../<?php echo htmlspecialchars($documento); ?>"
                               target="_blank"
                               class="documento-link">
                                <i class="fas fa-file-alt"></i>
                                <span><?php echo htmlspecialchars(basename($documento)); ?></span>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>
