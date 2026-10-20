<?php
// Iniciar la sesión para controlar si el usuario ya está autenticado
session_start();

// Si ya hay una sesión iniciada, redirigir a events.php
$currentUsername = $_SESSION['current-user'] ?? '';
$isLoggedOn = !empty($currentUsername);
if ($isLoggedOn) {
    header('Location: events.php', true, 302);
    exit();
}

// Incluir dependencias necesarias
require_once __DIR__ . '/data-access/CalendarDataAccess.php';
require_once __DIR__ . '/entities/User.php';
require_once __DIR__ . '/entities/Event.php';

$errorMsg = "";
$successMsg = "";

// Comprobar si el formulario ha sido enviado por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y limpiar los datos del formulario
    $email = trim($_POST['email'] ?? '');
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $birthDate = trim($_POST['birth_date'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $about = "";

    // 1. Validaciones de campos obligatorios y formato
    if (empty($email) || empty($firstName) || empty($lastName) || empty($birthDate) || empty($password) || empty($confirmPassword)) {
        $errorMsg = "Todos los campos obligatorios deben ser rellenados.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "El formato del correo electrónico no es válido.";
    } elseif (strlen($password) < 8) {
        $errorMsg = "La contraseña debe tener al menos 8 caracteres.";
    } elseif (!preg_match('/[0-9]/', $password) || !preg_match('/[a-zA-Z]/', $password)) {
        $errorMsg = "La contraseña debe contener al menos un número y una letra.";
    } elseif ($password !== $confirmPassword) {
        $errorMsg = "Las contraseñas no coinciden.";
    } else {

        // Ruta del archivo SQLite (ajustar si es necesario)
        $dbFile = __DIR__ . '/calendar.db';
        $calendarDataAccess = new CalendarDataAccess($dbFile);

        // 2. Comprobar si ya existe un usuario con el correo electrónico introducido
        if (!empty($calendarDataAccess->getUserByEmail($email))) {
            $errorMsg = "Ya existe un usuario registrado con este correo electrónico.";
        } else {
            // 3. Crear el hash de la contraseña
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // 4. Instanciar el objeto User
            $newUser = new User(
                $email,
                $hashedPassword,
                $firstName,
                $lastName,
                $birthDate,
                $about
            );

            // 5. Guardar en la BD
            $created = $calendarDataAccess->createUser($newUser);

            if ($created) {
                $successMsg = "Usuario creado";
            } else {
                $errorMsg = "Hubo un error al registrar el usuario en la base de datos.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container py-5">

        <h2 class="card-title text-center mb-4">Registro de Usuario</h2>

        <!-- Mostrar mensaje de error si falla la validación o duplicado -->
        <?php if (!empty($errorMsg)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($errorMsg); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Mostrar mensaje de éxito tras registrar -->
        <?php if (!empty($successMsg)): ?>
            <div class="alert alert-success text-center my-3" role="alert">
                <h4 class="alert-heading"><?php echo htmlspecialchars($successMsg); ?></h4>
                <p class="mb-0 mt-2">
                    <a href="login.php" class="btn btn-outline-success">Volver a la página de login</a>
                </p>
            </div>
        <?php else: ?>
            <!-- Formulario de registro con estilos Bootstrap -->
            <form action="register.php" method="POST" novalidate>
                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>


                <div class="mb-3">
                    <label for="first_name" class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">Apellidos <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required>
                </div>


                <div class="mb-3">
                    <label for="birth_date" class="form-label">Fecha de nacimiento <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="birth_date" name="birth_date" value="<?php echo htmlspecialchars($_POST['birth_date'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <div class="form-text">Mínimo 8 caracteres. Debe contener letras y números.</div>
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Repetir contraseña <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" class="btn btn-primary">Registrarse</button>
            </form>

            <div class="text-center mt-3">
                <small class="text-muted">¿Ya tienes una cuenta? <a href="index.php" class="text-decoration-none">Inicia sesión</a></small>
            </div>
        <?php endif; ?>






    </div>

    
</body>

</html>