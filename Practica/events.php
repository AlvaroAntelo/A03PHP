<?php
// Iniciar sesión
session_start();

// Control de acceso: A esta página solo se puede acceder con sesión iniciada
$currentUsername = $_SESSION['current-user'] ?? '';
$isLoggedOn = !empty($currentUsername);
if (!$isLoggedOn) {
    header('Location: index.php', true, 302);
    exit();
}

// Incluir dependencias necesarias
require_once __DIR__ . '/data-access/CalendarDataAccess.php';
require_once __DIR__ . '/entities/User.php';
require_once __DIR__ . '/entities/Event.php';

$userId = (int)$_SESSION['current-user'];
$user = null;
$events = [];
$errorMsg = "";


    // Inicializar la capa de acceso a datos
    $dbFile = __DIR__ . '/calendar.db';
    $calendarDataAccess = new CalendarDataAccess($dbFile);

    // Obtener información del usuario conectado
    $user = $calendarDataAccess->getUserById($userId);

    // Si el usuario no existe en la BD por algún motivo, forzar logout
    if ($user === null) {
        session_regenerate_id();
        session_destroy();
        header('Location: index.php', true, 302);
        exit();
    }

    // Obtener TODOS los eventos del usuario conectado usando el método de CalendarDataAccess
    $events = $calendarDataAccess->getEventsByUserId($userId);


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Eventos - Agenda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 CDN para Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="bg-light">
<div class="container py-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Listado de Eventos de <?= $user->getFirstName() ." ". $user->getLastName()?></h2>
    </div>

    <!-- Mensaje de error si falla la lectura en la BD -->
    <?php if (!empty($errorMsg)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($errorMsg); ?>
        </div>
    <?php endif; ?>

    <!-- ENLACE SUPERIOR "Nuevo evento" -->
    <div class="mb-3">
        <a href="events-mod/new-event.php" class="btn btn-primary" title="Crear nuevo evento">
            <i class="fa-solid fa-plus" aria-hidden="true"></i>
            <i class="fa-solid fa-calendar-plus ms-1" aria-hidden="true"></i>
            <span class="visually-hidden">Nuevo evento</span>
            <span class="ms-1 d-none d-sm-inline">Nuevo evento</span>
        </a>
    </div>

    <!-- Tabla de eventos -->
    <div class="card shadow-sm mb-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Título</th>
                            <th scope="col">Descripción</th>
                            <th scope="col">Fecha Inicio</th>
                            <th scope="col">Fecha Fin</th>
                            <th scope="col" class="text-center" style="width: 130px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($events)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No tienes ningún evento programado. ¡Crea uno nuevo!
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($events as $event): ?>
                                <tr>
                                    <td class="fw-bold"><?php echo htmlspecialchars($event->getTitle()); ?></td>
                                    <td><?php echo nl2br(htmlspecialchars($event->getDescription() ?? '')); ?></td>
                                    <td><?php echo htmlspecialchars($event->getStartDate()); ?></td>
                                    <td><?php echo htmlspecialchars($event->getEndDate()); ?></td>
                                    <td class="text-center">
                                        <!-- Enlace Editar con icono y texto oculto para accesibilidad -->
                                        <a href="events-mod/edit-event.php?id=<?=$event->getId(); ?>"
                                           class="btn btn-warning btn-sm me-1" 
                                           title="Editar evento">
                                            <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                                            <span class="visually-hidden">Editar evento: <?php echo htmlspecialchars($event->getTitle()); ?></span>
                                        </a>

                                        <!-- Enlace Eliminar con icono y texto oculto para accesibilidad -->
                                        <a href="events-mod/delete-event.php?id=<?=$event->getId(); ?>"
                                           class="btn btn-danger btn-sm" 
                                           title="Eliminar evento">
                                            <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                                            <span class="visually-hidden">Eliminar evento: <?php echo htmlspecialchars($event->getTitle()); ?></span>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ENLACE INFERIOR "Nuevo evento" -->
    <div class="mt-3 mb-5">
        <a href="/Segundo/A03PHP/Practica/events-mod/new-event.php" class="btn btn-primary" title="Crear nuevo evento">
            <i class="fa-solid fa-plus" aria-hidden="true"></i>
            <i class="fa-solid fa-calendar-plus ms-1" aria-hidden="true"></i>
            <span class="visually-hidden">Nuevo evento</span>
            <span class="ms-1 d-none d-sm-inline">Nuevo evento</span>
        </a>
    </div>

</div>
<div>
    <p><a href="logout.php">Logout</a></p>
</div>

</body>
</html>