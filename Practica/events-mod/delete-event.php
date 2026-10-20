<?php
// Iniciar sesión
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
$hasError = false;
$errorMessage = "No se puede acceder al evento porque no existe o porque no tiene permisos para verlo";


    // Inicializar acceso a la BD SQLite
    $dbFile = __DIR__ . "/../calendar.db"; 
    $CalendarDataAcess = new CalendarDataAccess($dbFile);

    // 1. Obtener el evento por id
    if ($eventId > 0) {
        $event = $CalendarDataAcess->getEventById($eventId);
    }

    // 2. Comprobar seguridad: que exista y pertenezca al usuario conectado
    if ($event === null || (int)$event->getUserId() !== $userId) {
        $hasError = true;
    }



// 3. Procesar el formulario enviado a la misma página
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$hasError) {
    // Si se pulsó el botón "Sí, eliminar el evento"
    if (isset($_POST['confirm_delete'])) {
        $CalendarDataAcess->deleteEvent($eventId);
    }
    
    // Tanto si confirma la eliminación como si cancela (o pulsa "No"), redirige al listado
    header("Location: ../events.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Evento - Agenda</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 CDN para Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">

            <?php if ($hasError): ?>
                <!-- Mensaje de Error / Permisos / Evento No Encontrado -->
                <div class="card border-danger shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="text-danger mb-3">
                            <i class="fa-solid fa-triangle-exclamation fa-3x" aria-hidden="true"></i>
                        </div>
                        <h4 class="card-title text-danger mb-3">Error de Acceso</h4>
                        <p class="card-text mb-4"><?php echo htmlspecialchars($errorMessage); ?></p>
                        <a href="../events.php" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-arrow-left me-1" aria-hidden="true"></i>Volver al listado de eventos
                        </a>
                    </div>
                </div>

            <?php else: ?>
                <!-- Confirmación de Eliminación del Evento -->
                <div class="card shadow-sm border-warning">
                    <div class="card-body p-4 text-center">
                        <div class="text-warning mb-3">
                            <i class="fa-solid fa-circle-question fa-3x" aria-hidden="true"></i>
                        </div>

                        <h3 class="card-title h4 mb-3">¿Seguro que desea eliminar el evento <?php echo htmlspecialchars($event->getTitle()); ?>?</h3>
                        
                        <!-- Resumen del evento a eliminar -->
                        <div class="bg-light p-3 rounded text-start mb-4 border">
                            <p class="mb-1"><strong>Título:</strong> <?php echo htmlspecialchars($event->getTitle()); ?></p>
                            <?php if (!empty($event->getDescription())): ?>
                                <p class="mb-1"><strong>Descripción:</strong> <?php echo htmlspecialchars($event->getDescription()); ?></p>
                            <?php endif; ?>
                            <p class="mb-1"><strong>Inicio:</strong> <?php echo htmlspecialchars($event->getStartDate()); ?></p>
                            <p class="mb-0"><strong>Fin:</strong> <?php echo htmlspecialchars($event->getEndDate()); ?></p>
                        </div>

                        <!-- Formulario enviado a la misma página -->
                        <form action="delete-event.php?id= <?=$eventId ?>"; method="POST">
                            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                                <!-- Botón Confirmar Eliminación -->
                                <button type="submit" name="confirm_delete" value="1" class="btn btn-danger me-sm-2">
                                    <i class="fa-solid fa-trash-can me-1" aria-hidden="true"></i>Sí, eliminar el evento
                                </button>
                                
                                <!-- Botón Cancelar (envía formulario o enlaza directamente) -->
                                <button type="submit" name="cancel_delete" value="1" class="btn btn-secondary">
                                    <i class="fa-solid fa-xmark me-1" aria-hidden="true"></i>No, volver al listado de eventos
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