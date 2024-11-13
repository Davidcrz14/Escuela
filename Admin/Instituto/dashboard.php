<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['pertenece'] !== 'Instituto') {
    header("Location: ../login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Obtener información del instituto
$instituto_id = $_SESSION['instituto_id'];
$query_instituto = "SELECT * FROM institutos WHERE id = :id";
$stmt_instituto = $db->prepare($query_instituto);
$stmt_instituto->bindParam(':id', $instituto_id);
$stmt_instituto->execute();
$instituto = $stmt_instituto->fetch(PDO::FETCH_ASSOC);

// Verificar si se encontró información del instituto
if (!$instituto) {
    // Manejar el caso cuando no se encuentra información del instituto
    echo "No se encontró información del instituto.";
    exit();
}

// Obtener noticias del instituto
$query_noticias = "SELECT n.*, a.username AS creado_por
                   FROM instituto_noticias n
                   JOIN admins a ON n.creado_por = a.id
                   WHERE n.instituto_id = :instituto_id
                   ORDER BY n.fecha_creacion DESC";
$stmt_noticias = $db->prepare($query_noticias);
$stmt_noticias->bindParam(':instituto_id', $instituto_id);
$stmt_noticias->execute();

?>

<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Instituto - <?php echo htmlspecialchars($instituto['nombre']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/instituto.css">
</head>
<body class="h-full">
    <!-- Similar al dashboard de Rectoría pero adaptado para Instituto -->
    <nav class="navbar">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center">
                    <a href="#" class="flex-shrink-0">
                        <img class="h-12 w-12" src="../<?php echo htmlspecialchars($instituto['imagen']); ?>" alt="Instituto Logo">
                    </a>
                    <div class="ml-4 text-xl font-bold text-white">
                        <i class="fas fa-university mr-2"></i><?php echo htmlspecialchars($instituto['nombre']); ?>
                    </div>
                </div>
                <div class="ml-4 flex items-center md:ml-6">
                    <a href="logout.php" class="btn-primary">
                        <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main>
        <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
            <!-- Información del Instituto -->
            <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
                <h2 class="text-2xl font-bold mb-4">Información del Instituto</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p><strong>Jefe de Carrera:</strong> <?php echo htmlspecialchars($instituto['jefe_carrera']); ?></p>
                        <p><strong>Contacto:</strong> <?php echo htmlspecialchars($instituto['contacto']); ?></p>
                    </div>
                    <div>
                        <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($instituto['ubicacion']); ?></p>
                        <p><strong>Horario:</strong> <?php echo htmlspecialchars($instituto['horario']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Formulario para crear noticias -->
            <div class="card">
                <div class="px-4 py-5 sm:p-6">
                    <h2 class="card-title">
                        <i class="fas fa-plus-circle mr-2"></i>Crear Nueva Publicación
                    </h2>
                    <form action="crear_publicacion.php" method="POST" enctype="multipart/form-data" class="mt-5 space-y-6">
                        <!-- Similar al formulario de Rectoría -->
                        <!-- ... -->
                    </form>
                </div>
            </div>

            <!-- Lista de noticias del instituto -->
            <div class="mt-6">
                <h2 class="text-2xl font-bold mb-4">Noticias Publicadas</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php while ($noticia = $stmt_noticias->fetch(PDO::FETCH_ASSOC)) {
                        $imagen_principal = str_replace('../../', '../', $noticia['imagen_principal']);
                    ?>
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                            <img src="<?php echo htmlspecialchars($imagen_principal); ?>" alt="<?php echo htmlspecialchars($noticia['titulo']); ?>" class="w-full h-48 object-cover">
                            <div class="p-4">
                                <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($noticia['titulo']); ?></h3>
                                <p class="text-gray-600 text-sm mb-4">Publicado por: <?php echo htmlspecialchars($noticia['creado_por']); ?></p>
                                <!-- ... -->
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
