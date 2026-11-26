<?php
require __DIR__ . '/../app/bootstrap.php';
$u = requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // VULN-IDOR: el id del usuario a modificar llega desde el formulario.
    // VULN-CSRF y cambio de correo/clave sin pedir la contraseña actual.
    $id = $_POST['id'] ?? $u['id'];
    db()->prepare('UPDATE users SET email = ?, bio = ? WHERE id = ?')
        ->execute([$_POST['email'] ?? '', $_POST['bio'] ?? '', $id]);
    if (!empty($_POST['password'])) {
        db()->prepare('UPDATE users SET password = ? WHERE id = ?')->execute([md5($_POST['password']), $id]);
    }
    header('Location: profile.php');
    exit;
}

top('Mi perfil');
// VULN-XSS almacenado en correo y biografía.
echo "<div>{$u['bio']}</div>
<form method='post'>
  <input type='hidden' name='id' value='{$u['id']}'>
  <label>Correo <input name='email' value='{$u['email']}'></label>
  <label>Biografía <textarea name='bio'>{$u['bio']}</textarea></label>
  <label>Nueva contraseña <input type='password' name='password'></label>
  <button>Guardar</button>
</form>";
bottom();
