<?php
require __DIR__ . '/../app/bootstrap.php';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // VULN-VALIDATION: sin reglas de longitud, formato ni complejidad.
    // VULN-MASS-ASSIGNMENT: se aceptan "role" y "balance" enviados por el cliente.
    try {
        $st = db()->prepare('INSERT INTO users (username,password,email,role,balance) VALUES (?,?,?,?,?)');
        $st->execute([
            $_POST['username'] ?? '',
            md5($_POST['password'] ?? ''),
            $_POST['email'] ?? '',
            $_POST['role'] ?? 'cliente',
            $_POST['balance'] ?? 100,
        ]);
        $msg = 'Cuenta creada.';
    } catch (PDOException $e) {
        $msg = $e->getMessage();
    }
}

top('Registro');
echo '<p>' . htmlspecialchars($msg) . '</p>';
?>
<form method="post">
  <label>Usuario <input name="username"></label>
  <label>Correo <input name="email"></label>
  <label>Contraseña <input type="password" name="password"></label>
  <button>Crear cuenta</button>
</form>
<p><a href="index.php">Volver</a></p>
<?php bottom();
