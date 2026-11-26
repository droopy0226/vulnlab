<?php
require __DIR__ . '/../app/bootstrap.php';
requireLogin();
top('Administración');
// VULN-ACCESS: la autorización depende de una cookie editable.
if (($_COOKIE['role'] ?? '') !== 'admin') {
    echo '<p>Acceso denegado</p>';
} else {
    echo '<table><tr><th>ID</th><th>Usuario</th><th>Hash</th><th>Rol</th><th>Saldo</th></tr>';
    foreach (db()->query('SELECT * FROM users') as $r) {
        // VULN-INFO: expone hashes de contraseñas.
        echo '<tr><td>' . $r['id'] . '</td><td>' . htmlspecialchars($r['username']) . '</td><td>' . $r['password']
            . '</td><td>' . htmlspecialchars($r['role']) . '</td><td>' . $r['balance'] . '</td></tr>';
    }
    echo '</table>';
}
bottom();
