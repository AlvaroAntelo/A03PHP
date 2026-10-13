<?php

require_once __DIR__ . "/../entities/User.php";
require_once __DIR__ . "/../entities/Event.php";

class CalendarDataAccess
{
    private $pdo;

    public function __construct($dbFile)
    {
        $this->pdo = new PDO("sqlite:" . $dbFile);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->createTables();
        $this->createInitialDataIfTablesEmpty();
    }

    private function createTables(): void
    {
        // Crear tabla de users
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT,
                                                            email TEXT UNIQUE,
                                                            password TEXT,
                                                            first_name TEXT,
                                                            last_name TEXT,
                                                            birth_date TEXT,
                                                            about TEXT)");

        // Crear tabla de eventos
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS events (id INTEGER PRIMARY KEY AUTOINCREMENT,
                                                            user_id INTEGER,
                                                            title TEXT,
                                                            description TEXT,
                                                            start_date TEXT,
                                                            end_date TEXT,
                                                            FOREIGN KEY(user_id) REFERENCES users(id))");
    }

    private function createInitialDataIfTablesEmpty(): void
    {
        if ($this->isUsersTableEmpty()) {
            $this->createInitialUsers();
        }
        if ($this->isEventsTableEmpty()) {
            $this->createInitialEvents();
        }
    }

    private function isUsersTableEmpty(): bool
    {
        $stmt = $this->pdo->query("SELECT COUNT(1) as count FROM users");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] == 0; // True si la tabla está vacía
    }

    private function isEventsTableEmpty(): bool
    {
        $stmt = $this->pdo->query("SELECT COUNT(1) as count FROM events");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] == 0; // True si la tabla está vacía
    }

    private function createInitialUsers(): void
    {
        $this->pdo->exec("INSERT INTO users (email, password, first_name, last_name, birth_date, about) VALUES 
                                                ('juan.perez@example.com', '" . password_hash('password123', PASSWORD_DEFAULT) . "', 'Juan', 'Perez', '1985-02-15', 'user frecuente de la agenda'),
                                                ('maria.lopez@example.com', '" . password_hash('pass456', PASSWORD_DEFAULT) . "', 'Maria', 'Lopez', '1990-05-20', 'Prefiere eventos cortos'),
                                                ('carlos.gomez@example.com', '" . password_hash('abc789', PASSWORD_DEFAULT) . "', 'Carlos', 'Gomez', '1978-11-12', 'Gerente de proyectos'),
                                                ('ana.martinez@example.com', '" . password_hash('ana321', PASSWORD_DEFAULT) . "', 'Ana', 'Martinez', '1983-07-08', 'Trabaja a tiempo parcial'),
                                                ('luis.torres@example.com', '" . password_hash('luis654', PASSWORD_DEFAULT) . "', 'Luis', 'Torres', '1995-09-30', 'Estudiante universitario');");
    }

    private function createInitialEvents(): void
    {
        # Eventos para Juan Perez (id 1)
        $this->pdo->exec("INSERT INTO events (user_id, title, description, start_date, end_date) VALUES
                                                (1, 'Reunión de equipo', 'Reunión semanal con el equipo', '2024-10-21 09:00', '2024-10-21 10:00'),
                                                (1, 'Llamada con cliente', 'Discusión sobre nuevo proyecto', '2024-10-22 14:00', '2024-10-22 14:30'),
                                                (1, 'Revisión mensual', 'Revisión mensual de objetivos', '2024-10-23 11:00', '2024-10-23 12:00'),
                                                (1, 'Taller de productividad', 'Asistencia a taller de productividad', '2024-10-24 16:00', '2024-10-24 18:00'),
                                                (1, 'Cita médica', 'Chequeo anual con el médico', '2024-10-25 08:00', '2024-10-25 08:30');");

        # Eventos para Maria Lopez (id 2)
        $this->pdo->exec("INSERT INTO events (user_id, title, description, start_date, end_date) VALUES
                                                (2, 'Clase de yoga', 'Clase de yoga por la mañana', '2024-10-21 07:00', '2024-10-21 08:00'),
                                                (2, 'Reunión con socios', 'Revisión de acuerdos con socios', '2024-10-22 10:00', '2024-10-22 11:30'),
                                                (2, 'Almuerzo con el equipo', 'Comida con el equipo de trabajo', '2024-10-22 13:00', '2024-10-22 14:00');");


        # Eventos para Carlos Gomez (id 3)
        $this->pdo->exec("INSERT INTO events (user_id, title, description, start_date, end_date) VALUES
                                                (3, 'Visita a cliente', 'Presentación de proyecto a cliente', '2024-10-21 11:00', '2024-10-21 13:00'),
                                                (3, 'Conferencia en línea', 'Asistencia a conferencia de tecnología', '2024-10-21 15:00', '2024-10-21 17:00'),
                                                (3, 'Revisión de contrato', 'Revisión de términos de contrato', '2024-10-23 10:00', '2024-10-23 11:00'),
                                                (3, 'Cena con cliente', 'Cena con el cliente tras la presentación', '2024-10-23 19:00', '2024-10-23 21:00'),
                                                (3, 'Actualización de proyecto', 'Actualización semanal del proyecto', '2024-10-24 09:00', '2024-10-24 10:00');");

        # Eventos para Ana Martinez (id 4)
        $this->pdo->exec("INSERT INTO events (user_id, title, description, start_date, end_date) VALUES
                                                (4, 'Trabajo remoto', 'Jornada de trabajo desde casa', '2024-10-21 08:00', '2024-10-21 15:00'),
                                                (4, 'Cita en el banco', 'Firma de documentos en el banco', '2024-10-22 09:00', '2024-10-22 09:30');");

        # Eventos para Luis Torres (id 5)
        $this->pdo->exec("INSERT INTO events (user_id, title, description, start_date, end_date) VALUES
                                                (5, 'Examen de matemáticas', 'Examen de fin de semestre', '2024-10-21 10:00', '2024-10-21 12:00'),
                                                (5, 'Clase de historia', 'Clase de historia contemporánea', '2024-10-22 11:00', '2024-10-22 13:00'),
                                                (5, 'Partido de fútbol', 'Partido amistoso con amigos', '2024-10-23 17:00', '2024-10-23 18:30'),
                                                (5, 'Estudio en biblioteca', 'Sesión de estudio para el examen final', '2024-10-24 09:00', '2024-10-24 12:00'),
                                                (5, 'Reunión del club de lectura', 'Discusión sobre el libro del mes', '2024-10-25 18:00', '2024-10-25 19:30'),
                                                (5, 'Clase de programación', 'Introducción a algoritmos en Python', '2024-10-26 14:00', '2024-10-26 16:00'),
                                                (5, 'Proyectos grupales', 'Reunión para trabajos grupales', '2024-10-27 11:00', '2024-10-27 13:00');");
    }

    // Obtener un usuario por ID
    public function getUserById(int $userId): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $user = new User(
                $result['email'],
                $result['password'],
                $result['first_name'],
                $result['last_name'],
                $result['birth_date'],
                $result['about'],
                $result['id']
            );
            return $user;
        } else {
            return null; // Devuelve null si no se encuentra el usuario
        }
    }

    // Obtener un usuario por su correo electrónico
    public function getUserByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $user = new User(
                $result['email'],
                $result['password'],
                $result['first_name'],
                $result['last_name'],
                $result['birth_date'],
                $result['about'],
                $result['id']
            );
            return $user;
        } else {
            return null; // Retorna null si no se encuentra el usuario
        }
    }

    // Obtener todos los usuarios
    public function getAllUsers(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY email ASC");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $users = [];

        foreach ($results as $row) {
            $user = new User(
                $row['email'],
                $row['password'],
                $row['first_name'],
                $row['last_name'],
                $row['birth_date'],
                $row['about'],
                $row['id'],
            );
            // Añadir usuario al array. Se podría hacer con array_push también.
            // También se podría hacer usando el id del usuario como clave, y el objeto como valor.
            $users[] = $user;
        }
        return $users;
    }

    // Crear un usuario. El atributo id se ignora, porque la BD lo asigna automáticamente
    public function createUser(User $user): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO users (email, password, first_name, last_name, birth_date, about) 
                                        VALUES (:email, :password, :first_name, :last_name, :birth_date, :about)");

        $stmt->bindValue(':email', $user->getEmail());
        $stmt->bindValue(':password', $user->getPassword());
        $stmt->bindValue(':first_name', $user->getFirstName());
        $stmt->bindValue(':last_name', $user->getLastName());
        $stmt->bindValue(':birth_date', $user->getBirthDate());
        $stmt->bindValue(':about', $user->getAbout());

        // Devuelve true si la sentencia se ejecuta correctamente
        return $stmt->execute();
    }

    // Actualizar un usuario de la BD
    public function updateUser(User $user): bool
    {
        $stmt = $this->pdo->prepare("UPDATE users SET email = :email, password = :password, first_name = :first_name, 
                                        last_name = :last_name, birth_date = :birth_date, about = :about WHERE id = :id");

        $stmt->bindValue(':email', $user->getEmail());
        $stmt->bindValue(':password', $user->getPassword());
        $stmt->bindValue(':first_name', $user->getFirstName());
        $stmt->bindValue(':last_name', $user->getLastName());
        $stmt->bindValue(':birth_date', $user->getBirthDate());
        $stmt->bindValue(':about', $user->getAbout());
        $stmt->bindValue(':id', $user->getId());

        // Devuelve true si la sentencia se ejecuta correctamente
        return $stmt->execute();
    }

    // Eliminar un usuario por su id
    public function deleteUserById(int $userId): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindValue(':id', $userId);

        // Devuelve true si la sentencia se ejecuta correctamente
        return $stmt->execute();
    }

    // Obtener un evento por id
    public function getEventById(int $eventId): ?Event
    {
        $stmt = $this->pdo->prepare("SELECT * FROM events WHERE id = :id");
        $stmt->bindValue(':id', $eventId);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $event = new Event(
                $row['user_id'],
                $row['title'],
                $row['description'],
                $row['start_date'],
                $row['end_date'],
                $row['id']
            );
            return $event;
        }
        return null;
    }

    // Obtener todos los eventos de un usuario.
    public function getEventsByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM events WHERE user_id = :user_id ORDER BY start_date ASC");
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $events = [];

        foreach ($results as $row) {
            $event = new Event(
                $row['user_id'],
                $row['title'],
                $row['description'],
                $row['start_date'],
                $row['end_date'],
                $row['id']
            );
            $events[] = $event;
        }
        return $events;
    }

    // Crear un evento.
    public function createEvent(Event $event): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO events (user_id, title, description, start_date, end_date) 
                                        VALUES (:user_id, :title, :description, :start_date, :end_date)");

        $stmt->bindValue(':user_id', $event->getUserId());
        $stmt->bindValue(':title', $event->getTitle());
        $stmt->bindValue(':description', $event->getDescription());
        $stmt->bindValue(':start_date', $event->getStartDate());
        $stmt->bindValue(':end_date', $event->getEndDate());

        return $stmt->execute();
    }

    // Actualizar un evento.
    public function updateEvent(Event $event): bool
    {
        $stmt = $this->pdo->prepare("UPDATE events SET title = :title, description = :description, 
                                        start_date = :start_date, end_date = :end_date WHERE id = :id");

        $stmt->bindValue(':title', $event->getTitle());
        $stmt->bindValue(':description', $event->getDescription());
        $stmt->bindValue(':start_date', $event->getStartDate());
        $stmt->bindValue(':end_date', $event->getEndDate());
        $stmt->bindValue(':id', $event->getId());

        return $stmt->execute();
    }

    // Eliminar un evento
    public function deleteEvent(int $eventId): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM events WHERE id = :id");
        $stmt->bindValue(':id', $eventId);

        return $stmt->execute();
    }
}
