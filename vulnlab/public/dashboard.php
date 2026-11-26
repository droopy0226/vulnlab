<?php
require __DIR__ . '/../app/bootstrap.php';
$u = requireLogin();
top('Panel principal');
// VULN-HTMLI / XSS reflejado: parámetro impreso sin codificar.
echo '<p>' . ($_GET['msg'] ?? 'Bienvenido') . ', ' . htmlspecialchars($u['username']) . '</p>';
echo '<p>Saldo: $' . number_format($u['balance'], 2) . '</p>';
bottom();
