<?php
// LABORATORIO DE ENTRENAMIENTO - CÓDIGO VULNERABLE A PROPÓSITO. NO PUBLICAR.

// Salvaguarda (esta sí es intencionalmente correcta): solo loopback y redes privadas.
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$publica = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
if (PHP_SAPI !== 'cli' && $publica !== false) {
    http_response_code(403);
    exit('Laboratorio disponible únicamente en entorno local.');
}

// VULN-AUTH: no se regenera el ID de sesión y la cookie no tiene HttpOnly ni SameSite.
ini_set('session.use_strict_mode', '0');
session_start();

function db(): PDO
{
    static $pdo = null;
    if ($pdo) {
        return $pdo;
    }
    $file = __DIR__ . '/../data/lab.sqlite';
    $nueva = !file_exists($file);
    $pdo = new PDO('sqlite:' . $file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if ($nueva) {
        seed($pdo);
    }
    return $pdo;
}

function seed(PDO $pdo): void
{
    $pdo->exec('CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT UNIQUE, password TEXT,
        email TEXT, role TEXT DEFAULT "cliente", balance REAL DEFAULT 100, bio TEXT DEFAULT "", reset_token TEXT)');
    $pdo->exec('CREATE TABLE comments (id INTEGER PRIMARY KEY, author TEXT, body TEXT)');
    $pdo->exec('CREATE TABLE orders (id INTEGER PRIMARY KEY, user_id INTEGER, product TEXT, total REAL)');

    // VULN-AUTH: contraseñas débiles y MD5 sin sal. Datos ficticios.
    $st = $pdo->prepare('INSERT INTO users (username,password,email,role,balance) VALUES (?,?,?,?,?)');
    $st->execute(['admin', md5('admin123'), 'admin@lab.test', 'admin', 5000]);
    $st->execute(['ana', md5('123456'), 'ana@lab.test', 'cliente', 300]);
    $st->execute(['carlos', md5('password'), 'carlos@lab.test', 'cliente', 150]);
    $pdo->exec("INSERT INTO orders (user_id,product,total) VALUES (1,'Licencia interna',900),(2,'Teclado',40),(3,'Monitor',120)");
}

function user(): ?array
{
    if (empty($_SESSION['uid'])) {
        return null;
    }
    $st = db()->prepare('SELECT * FROM users WHERE id = ?');
    $st->execute([$_SESSION['uid']]);
    return $st->fetch(PDO::FETCH_ASSOC) ?: null;
}

function requireLogin(): array
{
    $u = user();
    if (!$u) {
        header('Location: index.php');
        exit;
    }
    return $u;
}

function top(string $titulo): void
{
    echo '<!doctype html><html lang="es"><head><meta charset="utf-8"><title>' . htmlspecialchars($titulo)
        . '</title><link rel="stylesheet" href="assets/style.css"></head><body>'
        . '<div class="aviso">LABORATORIO VULNERABLE - Solo uso local y formativo</div>';
    if (user()) {
        echo '<nav><a href="dashboard.php">Inicio</a><a href="search.php">Buscar</a>'
            . '<a href="comments.php">Muro</a><a href="profile.php">Perfil</a><a href="transfer.php">Transferir</a>'
            . '<a href="shop.php">Tienda</a><a href="order.php?id=' . (int) $_SESSION['uid'] . '">Pedido</a>'
            . '<a href="admin.php">Admin</a><a href="logout.php">Salir</a></nav>';
    }
    echo '<main><h1>' . htmlspecialchars($titulo) . '</h1>';
}

function bottom(): void
{
    echo '</main></body></html>';
}
