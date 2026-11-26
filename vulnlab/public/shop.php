<?php
require __DIR__ . '/../app/bootstrap.php';
$u = requireLogin();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // VULN-LOGIC: precio y cantidad confiados al cliente; cupón reutilizable y acumulable.
    $total = (float) ($_POST['price'] ?? 0) * (float) ($_POST['qty'] ?? 1);
    if (($_POST['coupon'] ?? '') === 'LAB50') {
        $_SESSION['discount'] = ($_SESSION['discount'] ?? 0) + 50;
    }
    $total -= $_SESSION['discount'] ?? 0;
    if ($u['balance'] >= $total) {
        db()->prepare('UPDATE users SET balance = balance - ? WHERE id = ?')->execute([$total, $u['id']]);
        db()->prepare('INSERT INTO orders (user_id, product, total) VALUES (?, ?, ?)')
            ->execute([$u['id'], 'Teclado', $total]);
        $msg = "Compra registrada por $total";
        $u = user();
    } else {
        $msg = 'Saldo insuficiente';
    }
}

top('Tienda');
echo '<p>' . htmlspecialchars($msg) . '</p><p>Saldo: $' . number_format($u['balance'], 2) . '</p>';
?>
<form method="post">
  <p>Teclado - $80</p>
  <input type="hidden" name="price" value="80">
  <label>Cantidad <input name="qty" value="1"></label>
  <label>Cupón <input name="coupon" placeholder="LAB50"></label>
  <button>Comprar</button>
</form>
<?php bottom();
