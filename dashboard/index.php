<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Obtener noticias prioritarias
$query_prioritarias = "SELECT * FROM rectoria_noticias WHERE prioridad = 1 ORDER BY fecha_creacion DESC LIMIT 3";
$stmt_prioritarias = $db->prepare($query_prioritarias);
$stmt_prioritarias->execute();

// Obtener noticias normales
$query_normales = "SELECT * FROM rectoria_noticias WHERE prioridad = 0 ORDER BY fecha_creacion DESC";
$stmt_normales = $db->prepare($query_normales);
$stmt_normales->execute();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Universidad</title>
    <link rel="icon" href="logo.jpg" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <nav class="navbar text-white py-4">
        <div class="container mx-auto flex items-center justify-between">
            <div class="flex items-center">
                <img src="logo.jpg" alt="" class="w-12 h-12 rounded-full mr-4 animate__animated animate__pulse">
                <a class="text-xl font-bold" href="#">
                    <i class="fas fa-university me-2"></i>Universidad Tecnologica de la Mixteca
                </a>
            </div>
            <div class="flex items-center">
                <a href="editar_perfil.php" class="btn-nav mr-4">
                    <i class="fas fa-user-edit me-2"></i>Editar Perfil
                </a>
                <a href="../auth/logout.php" class="btn-nav">
                    <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                </a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto mt-8">
        <div class="carousel mb-8">
            <div class="carousel-inner">
                <?php
                $first = true;
                while ($noticia = $stmt_prioritarias->fetch(PDO::FETCH_ASSOC)) {
                    $imagen_principal = str_replace('../../', '../', $noticia['imagen_principal']);
                ?>
                    <div class="carousel-item <?php echo $first ? 'active' : ''; ?>">
                        <div class="relative">
                            <img src="<?php echo htmlspecialchars($imagen_principal); ?>" alt="<?php echo htmlspecialchars($noticia['titulo']); ?>" class="w-full h-96 object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent"></div>
                        </div>
                        <div class="carousel-caption">
                            <h3 class="text-3xl font-bold mb-2"><?php echo htmlspecialchars($noticia['titulo']); ?></h3>
                            <p class="text-lg mb-4"><?php echo substr(htmlspecialchars($noticia['informacion']), 0, 100) . '...'; ?></p>
                            <a href="noticia.php?id=<?php echo $noticia['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-book-reader me-2"></i>Leer más
                            </a>
                        </div>
                    </div>
                <?php
                    $first = false;
                }
                ?>
            </div>
            <button class="carousel-prev"><i class="fas fa-chevron-left"></i></button>
            <button class="carousel-next"><i class="fas fa-chevron-right"></i></button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php while ($noticia = $stmt_normales->fetch(PDO::FETCH_ASSOC)) {
                $imagen_principal = str_replace('../../', '../', $noticia['imagen_principal']);
            ?>
                <div class="card animate__animated animate__fadeInUp">
                    <div class="relative">
                        <img src="<?php echo htmlspecialchars($imagen_principal); ?>" alt="<?php echo htmlspecialchars($noticia['titulo']); ?>" class="w-full h-48 object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent"></div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($noticia['titulo']); ?></h5>
                        <p class="card-text"><?php echo substr(htmlspecialchars($noticia['informacion']), 0, 100) . '...'; ?></p>
                        <a href="noticia.php?id=<?php echo $noticia['id']; ?>" class="btn btn-secondary">
                            <i class="fas fa-book-open me-2"></i>Leer más
                        </a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <script src="../js/carousel.js"></script>
</body>
</html>
