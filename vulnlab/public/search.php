<?php
require __DIR__ . '/../app/bootstrap.php';
requireLogin();
$q = $_GET['q'] ?? '';
top('Buscar usuarios');
// VULN-XSS: valor reflejado en el atributo y en el texto.
echo "<form><input name='q' value='$q'><button>Buscar</button></form><p>Resultados para: $q</p><ul>";
try {
    // VULN-SQLI: LIKE construido por concatenación.
    foreach (db()->query("SELECT username, email FROM users WHERE username LIKE '%$q%'") as $r) {
        echo '<li>' . htmlspecialchars((string) $r['username']) . ' - ' . htmlspecialchars((string) $r['email']) . '</li>';
    }
} catch (PDOException $e) {
    echo '<li>' . $e->getMessage() . '</li>';
}
echo '</ul>';
bottom();
