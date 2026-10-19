<?php
session_start();
$currentUsername = $_SESSION['current-user'] ?? '';
$isLoggedOn = !empty($currentUsername);
if ($isLoggedOn) {
    header('Location: events.php', true, 302);
    exit();
}
require_once __DIR__ . '/data-access/CalendarDataAccess.php';
require_once __DIR__ . '/entities/User.php';
require_once __DIR__ . '/entities/Event.php';
$username = '';
$password = '';
$errors = [];
$recuerdame_cookie_value = $_COOKIE['recuerdame'] ?? '';
if(!empty($recuerdame_cookie_value)){
    $username=$recuerdame_cookie_value;
}

$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';

if ($isPost) {
    $dbFile = __DIR__ . '/calendar.db';
    $calendarDataAccess = new CalendarDataAccess($dbFile);
    $recuerdame_form_value = $_POST['recuerdame'] ?? '';
    if(empty($recuerdame_form_value)){
        setcookie('recuerdame','',time()-3600);
        $username='';
    } else {
        setcookie('recuerdame',$username ,time()+9999);
    }
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $user = $calendarDataAccess->getUserByEmail($username) ?? '';
   
    // Verificar si el usuario existe
    if (!empty($user) && password_verify($password, $user->getPassword())) {
        $_SESSION['current-user'] = $username;
        //header('Location: events.php', true, 302);
        exit();
    } else {
        $errors[] = "No existe el usuario o la contraseña es incorrecta.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Index</title>
</head>
<?php


?>

<body>
    <div class="container">
        <h1>Acceso al sistema</h1>


        <?php if (!empty($errors)): ?>
            <p>Se han producido errores</p>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach ?>
            </ul>
        <?php endif ?>

        <form method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Email</label>
                <input type="text" class="form-control" id="username" name="username" value=<?= $username ?>>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="recuerdame" name="recuerdame" <?php if(!empty($recuerdame_cookie_value)){echo "checked";} ?>>
                <label class="form-check-label" for="recuerdame">Recuérdame</label>
            </div>
            <button type="submit" class="btn btn-primary">Aceptar</button>
        </form>
        <p><a href="register.php">Crear cuenta</a></p>
    </div>
</body>

</html>