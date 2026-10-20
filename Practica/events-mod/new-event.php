<?php
// Iniciar la sesión para verificar el usuario autenticado
session_start();

// Requisito: Control de sesión (solo accesible con sesión iniciada)
$currentUsername = $_SESSION['current-user'] ?? '';
$isLoggedOn = !empty($currentUsername);
if (!$isLoggedOn) {
    header('Location: ../index.php', true, 302);
    exit();
}

// Incluir las clases necesarias
require_once __DIR__ . "/../data-access/CalendarDataAccess.php";
require_once __DIR__ . "/../entities/Event.php";
require_once __DIR__ . "/../entities/User.php";

$userId = (int)$_SESSION['current-user'];
$errorMsg = "";

// Inicializar variables con los valores del POST para mantenerlos si falla la validación
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$startDate = $_POST['start_date'] ?? '';
$endDate = $_POST['end_date'] ?? '';

// Comprobar si el formulario ha sido enviado por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Validaciones
    if (empty($title) || empty($startDate) || empty($endDate)) {
        $errorMsg = "Por favor, completa todos los campos obligatorios (Título, Fecha/Hora Inicio y Fecha/Hora Fin).";
    } elseif (strtotime($endDate) <= strtotime($startDate)) {
        $errorMsg = "La fecha y hora de fin debe ser estrictamente posterior a la fecha y hora de inicio.";
    } else {
        
            // Inicializar la capa de acceso a datos
            $dbFile = __DIR__ . "/../calendar.db"; 
            $CalendarDataAcess = new CalendarDataAccess($dbFile);

            // Formatear las fechas si provienen del control de tipo datetime-local (e.g. YYYY-MM-DDTHH:MM -> YYYY-MM-DD HH:MM)
            $formattedStart = str_replace('T', ' ', $startDate);
            $formattedEnd = str_replace('T', ' ', $endDate);

            // 2. Instanciar el objeto Event
            // Parámetros: ($userId, $title, $description, $startDate, $endDate, $id = null)
            $newEvent = new Event(
                $userId,
                $title,
                $description,
                $formattedStart,
                $formattedEnd
            );

            // 3. Crear el nuevo evento asociado al usuario conectado
            $created = $CalendarDataAcess->createEvent($newEvent);

            if ($created) {
                // Si la creación es exitosa, redirigir al listado de eventos
                header("Location: ../events.php");
                exit();
            } else {
                $errorMsg = "No se pudo guardar el evento en la base de datos.";
            }

        
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Evento - Agenda</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 CDN para Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="card-title text-center mb-4">
                        <i class="fa-solid fa-calendar-plus text-primary me-2" aria-hidden="true"></i>Crear Nuevo Evento
                    </h2>

                    <!-- Mostrar mensaje de error si la validación falla -->
                    <?php if (!empty($errorMsg)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-2" aria-hidden="true"></i>
                            <?php echo htmlspecialchars($errorMsg); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Formulario enviado a la misma página -->
                    <form action="new-event.php" method="POST" novalidate>
                        
                        <!-- Título del evento (Obligatorio) -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Título del evento <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="title" 
                                   name="title" 
                                   value="<?php echo htmlspecialchars($title); ?>" 
                                   required>
                        </div>

                        <!-- Descripción (Opcional - Textarea) -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      rows="3"><?php echo htmlspecialchars($description); ?></textarea>
                        </div>

                        <!-- Fecha y hora de inicio (Control nativo datetime-local) -->
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Fecha y hora de inicio <span class="text-danger">*</span></label>
                            <input type="datetime-local" 
                                   class="form-control" 
                                   id="start_date" 
                                   name="start_date" 
                                   value="<?php echo htmlspecialchars($startDate); ?>" 
                                   required>
                        </div>

                        <!-- Fecha y hora de fin (Control nativo datetime-local) -->
                        <div class="mb-4">
                            <label for="end_date" class="form-label">Fecha y hora de fin <span class="text-danger">*</span></label>
                            <input type="datetime-local" 
                                   class="form-control" 
                                   id="end_date" 
                                   name="end_date" 
                                   value="<?php echo htmlspecialchars($endDate); ?>" 
                                   required>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="../events.php" class="btn btn-outline-secondary me-md-2">
                                <i class="fa-solid fa-xmark me-1" aria-hidden="true"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-floppy-disk me-1" aria-hidden="true"></i>Guardar Evento
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>