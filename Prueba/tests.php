<?php

require_once __DIR__ . '/data-access/CalendarDataAccess.php';
require_once __DIR__ . '/entities/User.php';
require_once __DIR__ . '/entities/Event.php';

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pruebas de CalendarDataAccess</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

</head>

<body class="container">


    <h1>Probando CalendarDataAccess</h1>

    <h2>Ubicación del fichero php.ini</h2>
    <p>
        <?= php_ini_loaded_file() ?>
    </p>
    <p>Si no hay fichero .ini, hay que copiar el fichero ini "development", y renombrarlo a php.ini.</p>
    <p>Modificar el fichero para hacer los siguientes cambios:</p>
    <ul>
        <li>Descomentar la línea <em>;extension_dir = "ext"</em>, quitando el punto y coma.</li>
        <li>Descomentar la línea <em>;extension=pdo_sqlite</em>, quitando el punto y coma.</li>
    </ul>

    <?php

    // Ruta del fichero de la BD
    $dbFile = __DIR__ . '/calendar.db';

    // Borrar el fichero de la BD si existe, para que las pruebas funcionen sin errores
    if (file_exists($dbFile)) {
        // Eliminar el archivo
        unlink($dbFile);
    }

    // Crear un objeto para acceso a la BD
    $calendarDataAccess = new CalendarDataAccess($dbFile);

    // Inicializar resultados
    $results = [];

    // 1. Obtener todos los usuarios
    echo "<h2>Todos los usuarios</h2><ul>";
    $users = $calendarDataAccess->getAllUsers();
    foreach ($users as $user) {
        echo "<li>ID: {$user->getId()}, Nombre: {$user->getFirstName()} {$user->getLastName()}, Email: {$user->getEmail()}</li>";
    }
    echo "</ul>";

    // 2. Crear un nuevo usuario
    $newUser = new User('new.user@example.com',  password_hash('newpassword', PASSWORD_DEFAULT), 'Nuevo', 'Usuario', '1992-01-01', 'Un nuevo usuario.');
    $created = $calendarDataAccess->createUser($newUser);
    echo "<h2>Crear usuario</h2>" . ($created ? "Usuario creado." : "Error al crear usuario.");

    // 3. Obtener un usuario por ID
    echo "<h2>Usuario por su id</h2>";
    $user = $calendarDataAccess->getUserById(1);
    echo $user ? "Usuario encontrado: {$user->getFirstName()} {$user->getLastName()}" : "Usuario no encontrado.";

    // 4. Actualizar un usuario
    $userToUpdate = new User('updated.user@example.com', password_hash('updatedpassword', PASSWORD_DEFAULT), 'Usuario', 'Actualizado', '1990-01-01', 'Un usuario actualizado.', 1);
    $updated = $calendarDataAccess->updateUser($userToUpdate);
    echo "<h2>Actualizar usuario</h2>" . ($updated ? "Usuario actualizado." : "Error al actualizar usuario.");

    // 5. Obtener todos los usuarios, otra vez
    echo "<h2>Todos los usuarios</h2><ul>";
    $users = $calendarDataAccess->getAllUsers();
    foreach ($users as $user) {
        echo "<li>ID: {$user->getId()}, Nombre: {$user->getFirstName()} {$user->getLastName()}, Email: {$user->getEmail()}</li>";
    }
    echo "</ul>";

    // 6. Obtener todos los eventos de un usuario
    echo "<h2>Eventos por ID de usuario</h2><ul>";
    $events = $calendarDataAccess->getEventsByUserId(1);
    foreach ($events as $event) {
        echo "<li>ID: {$event->getId()}, Título: {$event->getTitle()}, Inicio: {$event->getStartDate()}</li>";
    }
    echo "</ul>";

    // 7. Crear un nuevo evento
    $newEvent = new Event(1, 'Nuevo evento', 'Descripción del nuevo evento', '2024-10-30 10:00', '2024-10-30 11:00');
    $eventCreated = $calendarDataAccess->createEvent($newEvent);
    echo "<h2>Crear evento</h2>" . ($eventCreated ? "Evento creado." : "Error al crear evento.");

    // 8. Obtener un evento por ID
    echo "<h2>Obtener evento por ID</h2>";
    $event = $calendarDataAccess->getEventById(1);
    echo $event ? "Evento encontrado: {$event->getTitle()}, Inicio: {$event->getStartDate()}" : "Evento no encontrado.";

    // 9. Actualizar un evento
    $eventToUpdate = new Event(1, 'Evento actualizado', 'Descripción actualizada', '2024-10-31 10:00', '2024-10-31 11:00');
    $eventUpdated = $calendarDataAccess->updateEvent($eventToUpdate);
    echo "<h2>Actualizar evento</h2>" . ($eventUpdated ? "Evento actualizado con éxito." : "Error al actualizar evento.");

    // 10. Obtener todos los eventos de un usuario
    echo "<h2>Obtener eventos por ID de usuario</h2><ul>";
    $events = $calendarDataAccess->getEventsByUserId(1);
    foreach ($events as $event) {
        echo "<li>ID del evento: {$event->getId()}, Título: {$event->getTitle()}, Inicio: {$event->getStartDate()}</li>";
    }
    echo "</ul>";

    // 11. Eliminar un evento
    echo "<h2>Eliminar evento</h2>";
    $eventDeleted = $calendarDataAccess->deleteEvent(1);
    echo $eventDeleted ? "Evento eliminado con éxito." : "Error al eliminar evento.";

    // 12. Eliminar un usuario
    echo "<h2>Eliminar usuario</h2>";
    $userDeleted = $calendarDataAccess->deleteUserById(1);
    echo $userDeleted ? "Usuario eliminado con éxito." : "Error al eliminar usuario.";

    ?>

</body>

</html>