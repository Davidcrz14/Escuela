<?php
session_start();
require_once '../config/database.php';

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

$user_id = intval($_SESSION['user_id']); // Convertir a entero para mayor seguridad

// Obtener los detalles del alumno usando consulta preparada
$query = "SELECT * FROM alumnos WHERE id = :id LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$alumno = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$alumno) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validación y limpieza de datos
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $correo = filter_input(INPUT_POST, 'correo', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    // Validaciones adicionales
    if (empty($username) || strlen($username) > 50) {
        $_SESSION['error'] = "El nombre de usuario es inválido";
        header("Location: editar_perfil.php");
        exit();
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "El correo electrónico no es válido";
        header("Location: editar_perfil.php");
        exit();
    }

    // Verificar si el username ya existe
    $check_query = "SELECT id FROM alumnos WHERE username = :username AND id != :id";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':username', $username);
    $check_stmt->bindParam(':id', $user_id);
    $check_stmt->execute();

    if ($check_stmt->rowCount() > 0) {
        $_SESSION['error'] = "El nombre de usuario ya está en uso";
        header("Location: editar_perfil.php");
        exit();
    }

    // Verificar si el correo ya existe
    $check_query = "SELECT id FROM alumnos WHERE correo = :correo AND id != :id";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':correo', $correo);
    $check_stmt->bindParam(':id', $user_id);
    $check_stmt->execute();

    if ($check_stmt->rowCount() > 0) {
        $_SESSION['error'] = "El correo electrónico ya está en uso";
        header("Location: editar_perfil.php");
        exit();
    }

    try {
        $db->beginTransaction();

        // Actualizar los detalles del alumno
        $query = "UPDATE alumnos SET username = :username, correo = :correo";
        $params = [
            ':username' => $username,
            ':correo' => $correo,
            ':id' => $user_id
        ];

        if (!empty($password)) {
            if (strlen($password) < 6) {
                $_SESSION['error'] = "La contraseña debe tener al menos 6 caracteres";
                header("Location: editar_perfil.php");
                exit();
            }
            $query .= ", password = :password";
            $params[':password'] = $password;
        }

        $query .= " WHERE id = :id";
        $stmt = $db->prepare($query);

        if ($stmt->execute($params)) {
            $db->commit();
            $_SESSION['success'] = "Perfil actualizado correctamente";
            header("Location: index.php");
            exit();
        } else {
            throw new Exception("Error al actualizar el perfil");
        }
    } catch (Exception $e) {
        $db->rollBack();
        $_SESSION['error'] = "Error al actualizar el perfil: " . $e->getMessage();
        header("Location: editar_perfil.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - Universidad</title>
    <link rel="icon" href="logo.jpg" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
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
            <div>
                <a class="text-white hover:underline" href="../auth/logout.php">
                    <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                </a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto mt-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-8">
                <h2 class="text-3xl font-bold mb-6 text-center">Editar Perfil</h2>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                        <?php
                        echo htmlspecialchars($_SESSION['error']);
                        unset($_SESSION['error']);
                        ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                        <?php
                        echo htmlspecialchars($_SESSION['success']);
                        unset($_SESSION['success']);
                        ?>
                    </div>
                <?php endif; ?>
                <form method="POST" onsubmit="return validateForm()">
                    <div class="mb-4">
                        <label for="username" class="block mb-2 font-bold text-gray-700">Username:</label>
                        <input type="text" id="username" name="username"
                               value="<?php echo htmlspecialchars($alumno['username']); ?>"
                               required
                               pattern="[a-zA-Z0-9_]{3,50}"
                               title="El username debe contener solo letras, números y guiones bajos"
                               class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none">
                    </div>
                    <div class="mb-4">
                        <label for="correo" class="block mb-2 font-bold text-gray-700">Correo:</label>
                        <input type="email" id="correo" name="correo"
                               value="<?php echo htmlspecialchars($alumno['correo']); ?>"
                               required
                               class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none">
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block mb-2 font-bold text-gray-700">Nueva Contraseña:</label>
                        <input type="password" id="password" name="password"
                               minlength="6"
                               class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none">
                        <p class="text-sm text-gray-500 mt-1">Dejar en blanco para mantener la contraseña actual</p>
                    </div>
                    <div class="mb-4">
                        <label class="block mb-2 font-bold text-gray-700">Matrícula:</label>
                        <input type="text" value="<?php echo htmlspecialchars($alumno['matricula']); ?>" disabled class="w-full px-3 py-2 text-gray-700 border rounded-lg bg-gray-100 cursor-not-allowed">
                    </div>
                    <div class="mb-6">
                        <label class="block mb-2 font-bold text-gray-700">Carrera:</label>
                        <input type="text" value="<?php echo htmlspecialchars($alumno['carrera']); ?>" disabled class="w-full px-3 py-2 text-gray-700 border rounded-lg bg-gray-100 cursor-not-allowed">
                    </div>
                    <div class="text-center">
                        <button type="submit" class="bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-600 transition duration-200">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function validateForm() {
        const username = document.getElementById('username').value;
        const correo = document.getElementById('correo').value;
        const password = document.getElementById('password').value;

        // Validación del username
        if (!/^[a-zA-Z0-9_]{3,50}$/.test(username)) {
            alert('El username debe contener solo letras, números y guiones bajos');
            return false;
        }

        // Validación del correo
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
            alert('Por favor, ingrese un correo electrónico válido');
            return false;
        }

        // Validación de la contraseña
        if (password !== '' && password.length < 6) {
            alert('La contraseña debe tener al menos 6 caracteres');
            return false;
        }

        return true;
    }
    </script>
</body>
</html>
