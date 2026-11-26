<?php
require __DIR__ . '/../app/bootstrap.php';
requireLogin();
// VULN-IDOR: no verifica que el pedido pertenezca al usuario autenticado.
$st = db()->prepare('SELECT * FROM orders WHERE id = ?');
$st->execute([$_GET['id'] ?? 0]);
$o = $st->fetch(PDO::FETCH_ASSOC);
top('Detalle de pedido');
echo $o
    ? '<p>Pedido #' . (int) $o['id'] . ' | Usuario ' . (int) $o['user_id'] . ' | '
        . htmlspecialchars($o['product']) . ' | $' . number_format($o['total'], 2) . '</p>'
    : '<p>No encontrado</p>';
bottom();
