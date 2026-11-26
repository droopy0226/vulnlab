<?php
require __DIR__ . '/../app/bootstrap.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'] ?? '';
    $p = md5($_POST['password'] ?? '');
    try {
        // VULN-SQLI: concatenación directa de la entrada en la consulta.
        $row = db()->query("SELECT * FROM users WHERE username = '$u' AND password = '$p'")->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $_SESSION['uid'] = $row['id']; // VULN-AUTH: fijación de sesión.
            setcookie('role', $row['role']); // VULN-ACCESS: rol controlado por el cliente.
            // VULN-VALIDATION: redirección abierta.
            header('Location: ' . ($_GET['next'] ?? 'dashboard.php'));
            exit;
        }
        // VULN-AUTH: enumeración de usuarios y sin límite de intentos.
        $existe = db()->query("SELECT 1 FROM users WHERE username = '$u'")->fetch();
        $error = $existe ? 'Contraseña incorrecta' : "El usuario $u no existe"; // VULN-XSS
    } catch (PDOException $e) {
        $error = $e->getMessage(); // VULN-INFO: errores de base de datos expuestos.
    }
}

top('Iniciar sesión');
echo "<p class='error'>$error</p>";
?>
<form method="post">
  <label>Usuario <input name="username"></label>
  <label>Contraseña <input type="password" name="password"></label>
  <button>Entrar</button>
</form>
<p><a href="register.php">Registrarse</a> · <a href="reset.php">Recuperar contraseña</a></p>
<?php bottom();
