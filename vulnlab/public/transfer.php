<?php
require __DIR__ . '/../app/bootstrap.php';
$u = requireLogin();
$msg = '';

if (isset($_REQUEST['to'], $_REQUEST['amount'])) {
    // VULN-CSRF: acepta GET y no usa token.
    // VULN-LOGIC: no valida signo, saldo, destinatario ni autotransferencia.
    // VULN-LOGIC: sin transacción atómica (condición de carrera).
    $amount = (float) $_REQUEST['amount'];
    db()->prepare('UPDATE users SET balance = balance - ? WHERE id = ?')->execute([$amount, $u['id']]);
    db()->prepare('UPDATE users SET balance = balance + ? WHERE username = ?')->execute([$amount, $_REQUEST['to']]);
    $msg = "Transferencia de $amount a {$_REQUEST['to']} realizada"; // VULN-XSS
    $u = user();
}

top('Transferir saldo');
echo "<p>$msg</p><p>Saldo: $" . number_format($u['balance'], 2) . '</p>';
?>
<form method="post">
  <label>Destinatario <input name="to"></label>
  <label>Monto <input name="amount"></label>
  <button>Enviar</button>
</form>
<?php bottom();
