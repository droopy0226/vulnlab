<?php
require __DIR__ . '/../app/bootstrap.php';
// VULN-AUTH: no destruye la sesión en el servidor ni borra la cookie "role".
unset($_SESSION['uid']);
header('Location: index.php');
