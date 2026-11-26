<?php
require __DIR__ . '/../app/bootstrap.php';
$msg = '';

if (isset($_POST['username'])) {
    // VULN-AUTH: token predecible, sin expiración y mostrado en pantalla.
    $token = md5($_POST['username'] . date('Ymd'));
    $st = db()->prepare('UPDATE users SET reset_token = ? WHERE username = ?');
    $st->execute([$token, $_POST['username']]);
    $msg = $st->rowCount() ? "Token (simula el correo): $token" : 'Usuario no encontrado';
} elseif (isset($_POST['token'])) {
    // VULN-AUTH: el token no se invalida después de usarse.
    $st = db()->prepare('UPDATE users SET password = ? WHERE reset_token = ?');
    $st->execute([md5($_POST['password'] ?? ''), $_POST['token']]);
    $msg = $st->rowCount() ? 'Contraseña actualizada' : 'Token inválido';
}

top('Recuperar contraseña');
echo '<p>' . htmlspecialchars($msg) . '</p>';
?>
<form method="post"><label>Usuario <input name="username"></label><button>Solicitar token</button></form>
<form method="post">
  <label>Token <input name="token"></label>
  <label>Nueva contraseña <input type="password" name="password"></label>
  <button>Cambiar</button>
</form>
<p><a href="index.php">Volver</a></p>
<?php bottom();
