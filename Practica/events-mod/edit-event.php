<?php
// Iniciar la sesión
session_start();

// Control de acceso: A esta página solo se puede acceder con sesión iniciada
$currentUsername = $_SESSION['current-user'] ?? '';
$isLoggedOn = !empty($currentUsername);
if (!$isLoggedOn) {
    header('Location: ../index.php', true, 302);
    exit();
}

// Incluir dependencias necesarias
require_once __DIR__ . "/../data-access/CalendarDataAccess.php";
require_once __DIR__ . "/../entities/Event.php";
require_once __DIR__ . "/../entities/User.php";

$userId = (int)$_SESSION['current-user'];
$eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$event = null;
$hasAccessError = false;
$errorMessage = "No se puede acceder al evento porque no existe o porque no tiene permisos para verlo";
$errorMsg = "";


    // Inicializar acceso a la BD SQLite
    $dbFile = __DIR__ . "/../calendar.db"; 
    $CalendarDataAcess = new CalendarDataAccess($dbFile);

    // 1. Obtener el evento por id
    if ($eventId > 0) {
        $event = $CalendarDataAcess->getEventById($eventId);
    }

    // 2. Comprobar seguridad: que exista y pertenezca al usuario conectado
    if ($event === null || (int)$event->getUserId() !== $userId) {
        $hasAccessError = true;
    }


// Inicializar variables del formulario
if (!$hasAccessError) {
    // Si es una petición GET, precargamos los datos desde la BD (convertidos para datetime-local)
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $title = $event->getTitle();
        $description = $event->getDescription();
        // Reemplazamos espacios por 'T' para que sea compatible con <input type="datetime-local"> (e.g. YYYY-MM-DDTHH:MM)
        $startDate = str_replace(' ', 'T', $event->getStartDate());
        $endDate = str_replace(' ', 'T', $event->getEndDate());
    } else {
        // Si es POST, recogemos los datos enviados por el usuario
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $startDate = $_POST['start_date'] ?? '';
        $endDate = $_POST['end_date'] ?? '';

        // 3. Validaciones
        if (empty($title) || empty($startDate) || empty($endDate)) {
            $errorMsg = "Por favor, completa todos los campos obligatorios (Título, Fecha/Hora Inicio y Fecha/Hora Fin).";
        } elseif (strtotime($endDate) <= strtotime($startDate)) {
            $errorMsg = "La fecha y hora de fin debe ser estrictamente posterior a la fecha y hora de inicio.";
        } else {
            
                // Formatear las fechas de vuelta a formato estándar (e.g. YYYY-MM-DD HH:MM)
                $formattedStart = str_replace('T', ' ', $startDate);
                $formattedEnd = str_replace('T', ' ', $endDate);

                // Actualizar las propiedades del objeto Event
                $event->setTitle($title);
                $event->setDescription($description);
                $event->setStartDate($formattedStart);
                $event->setEndDate($formattedEnd);

                // 4. Guardar los cambios en la base de datos
                $updated = $CalendarDataAcess->updateEvent($event);

                if ($updated) {
                    // Redirigir al listado de eventos tras modificar correctamente
                    header("Location: ../events.php");
                    exit();
                } else {
                    $errorMsg = "No se pudieron guardar los cambios en la base de datos.";
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
    <title>Editar Evento - Agenda</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 CDN para Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">

            <?php if ($hasAccessError): ?>
                <!-- Mensaje de Error de Permisos / Evento No Encontrado -->
                <div class="card border-danger shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="text-danger mb-3">
                            <i class="fa-solid fa-triangle-exclamation fa-3x" aria-hidden="true"></i>
                        </div>
                        <h4 class="card-title text-danger mb-3">Error de Acceso</h4>
                        <p class="card-text mb-4"><?php echo htmlspecialchars($errorMessage); ?></p>
                        <a href="events.php" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-arrow-left me-1" aria-hidden="true"></i>Volver al listado de eventos
                        </a>
                    </div>
                </div>

            <?php else: ?>
                <!-- Formulario de Edición de Evento -->
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="card-title text-center mb-4">
                            <i class="fa-solid fa-pen-to-square text-warning me-2" aria-hidden="true"></i>Modificar Evento
                        </h2>

                        <!-- Mostrar mensaje de error si falla la validación -->
                        <?php if (!empty($errorMsg)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fa-solid fa-circle-exclamation me-2" aria-hidden="true"></i>
                                <?php echo htmlspecialchars($errorMsg); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Formulario enviado a la misma página manteniendo el ID en la URL -->
                        <form action="edit-event.php?id=<?=$eventId?>" method="POST" novalidate>
                            
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
                                    <i class="fa-solid fa-floppy-disk me-1" aria-hidden="true"></i>Guardar Cambios
                                </button>
                            </div>

                        </form>

                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

</body>
</html>