<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['pertenece'] !== 'Rectoria') {
    header("Location: ../login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Obtener la lista de alumnos
$query_alumnos = "SELECT * FROM alumnos";
$stmt_alumnos = $db->prepare($query_alumnos);
$stmt_alumnos->execute();

?>

<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Rectoría - Universidad</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="rectoria.css">
    <link rel="icon" href="logo.jpg" type="image/png">
</head>
<body class="h-full">
    <div class="min-h-full">
        <nav class="navbar">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center">
                        <a href="#" class="flex-shrink-0">
                            <img class="h-12 w-12" src="logo.jpg" alt="Universidad Logo">
                        </a>
                        <div class="ml-4 text-xl font-bold text-white">
                            <i class="fas fa-university mr-2"></i>Universidad Panel - Rectoría
                        </div>
                    </div>
                    <div class="ml-4 flex items-center md:ml-6">
                        <a href="../logout.php" class="btn-primary">
                            <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </nav>



        <main>
            <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
                <div class="px-4 py-6 sm:px-0">
                    <div class="flex justify-end mb-4">
                        <button type="button" onclick="document.getElementById('alumnosModal').style.display='block'" class="btn-secondary">
                            <i class="fas fa-users mr-2"></i>Ver Lista de Alumnos
                        </button>
                    </div>

                    <div class="card">
                        <div class="px-4 py-5 sm:p-6">
                            <h2 class="card-title">
                                <i class="fas fa-plus-circle mr-2"></i>Crear Nueva Publicación
                            </h2>
                            <form action="crear_publicacion.php" method="POST" enctype="multipart/form-data" class="mt-5 space-y-6">
                                <div>
                                    <label for="titulo" class="block text-sm font-medium leading-6 text-gray-900">Título:</label>
                                    <input type="text" id="titulo" name="titulo" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <div>
                                    <label for="informacion" class="block text-sm font-medium leading-6 text-gray-900">Información:</label>
                                    <textarea id="informacion" name="informacion" rows="5" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"></textarea>
                                </div>
                                <div>
                                    <label for="imagen_principal" class="block text-sm font-medium leading-6 text-gray-900">Imagen Principal:</label>
                                    <input type="file" id="imagen_principal" name="imagen_principal" accept="image/*" required class="mt-2 block w-full text-sm text-gray-900 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                </div>
                                <div>
                                    <label for="imagenes" class="block text-sm font-medium leading-6 text-gray-900">Imágenes Adicionales:</label>
                                    <input type="file" id="imagenes" name="imagenes[]" accept="image/*" multiple class="mt-2 block w-full text-sm text-gray-900 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                </div>
                                <div>
                                    <label for="documentos" class="block text-sm font-medium leading-6 text-gray-900">Documentos:</label>
                                    <input type="file" id="documentos" name="documentos[]" multiple class="mt-2 block w-full text-sm text-gray-900 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                </div>
                                <div>
                                    <label for="prioridad" class="block text-sm font-medium leading-6 text-gray-900">Prioridad:</label>
                                    <select id="prioridad" name="prioridad" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <option value="0">Normal</option>
                                        <option value="1">Alta</option>
                                    </select>
                                </div>
                                <div>
                                    <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                        <i class="fas fa-paper-plane mr-2"></i>Crear Publicación
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal de Lista de Alumnos -->
    <div class="fixed z-10 inset-0 overflow-y-auto hidden" id="alumnosModal">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="alumnosModalLabel">
                                Lista de Alumnos
                            </h3>
                            <div class="mt-2">
                                <div class="flex flex-col">
                                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                                <table class="min-w-full divide-y divide-gray-200">
                                                    <thead class="bg-gray-50">
                                                        <tr>
                                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                ID
                                                            </th>
                                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Username
                                                            </th>
                                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Correo
                                                            </th>
                                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Matrícula
                                                            </th>
                                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                Carrera
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white divide-y divide-gray-200">
                                                        <?php while ($alumno = $stmt_alumnos->fetch(PDO::FETCH_ASSOC)) { ?>
                                                            <tr>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                    <?php echo htmlspecialchars($alumno['id']); ?>
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                    <?php echo htmlspecialchars($alumno['username']); ?>
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                    <?php echo htmlspecialchars($alumno['correo']); ?>
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                    <?php echo htmlspecialchars($alumno['matricula']); ?>
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                    <?php echo htmlspecialchars($alumno['carrera']); ?>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="btn-primary" onclick="document.getElementById('alumnosModal').style.display='none'">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
