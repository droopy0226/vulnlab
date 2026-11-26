<?php
require __DIR__ . '/../app/bootstrap.php';
$u = requireLogin();

if (!empty($_POST['body'])) {
    // VULN-CSRF: sin token. VULN-VALIDATION: sin límite de tamaño.
    db()->prepare('INSERT INTO comments (author, body) VALUES (?, ?)')->execute([$u['username'], $_POST['body']]);
    header('Location: comments.php');
    exit;
}

top('Muro de comentarios');
echo '<form method="post"><textarea name="body"></textarea><button>Publicar</button></form>';
foreach (db()->query('SELECT * FROM comments ORDER BY id DESC') as $c) {
    // VULN-XSS almacenado / inyección HTML.
    echo '<article><b>' . $c['author'] . '</b><div>' . $c['body'] . '</div></article>';
}
bottom();
