# Laboratorio de seguridad web en PHP

> **Advertencia:** la aplicación es vulnerable de forma intencional. Debe ejecutarse solo en un equipo local o en una red aislada, con datos ficticios, y nunca publicarse en un servidor accesible desde Internet.

## Propósito
El laboratorio permite a equipos de desarrollo, QA y seguridad reconocer fallas frecuentes, medir su impacto en el negocio y practicar su remediación. Las pruebas deben limitarse a este entorno.

## Ejecución

Con Docker:

    docker compose up

Con PHP 8 y la extensión `pdo_sqlite`:

    php -S 127.0.0.1:8080 -t public

Dirección: http://127.0.0.1:8080. La base de datos se crea en el primer acceso. Para reiniciar el laboratorio se elimina `data/lab.sqlite`.

| Usuario | Contraseña | Rol |
|---|---|---|
| admin | admin123 | admin |
| ana | 123456 | cliente |
| carlos | password | cliente |

## Estructura

    vulnlab/
    ├── app/bootstrap.php    Conexión, sesión, datos iniciales y plantilla
    ├── public/              Raíz web (login, registro, módulos)
    │   └── assets/style.css
    ├── data/                Base SQLite (se genera sola)
    └── docker-compose.yml

## Matriz de hallazgos

| # | Hallazgo | Archivo | Severidad | Impacto en el negocio | Remediación |
|---|---|---|---|---|---|
| 1 | Inyección SQL | index.php, search.php | Crítica | Acceso no autorizado y fuga de datos | Consultas preparadas |
| 2 | XSS reflejado | index.php, search.php, dashboard.php, transfer.php | Alta | Suplantación de sesiones | `htmlspecialchars` con `ENT_QUOTES`, CSP |
| 3 | XSS almacenado | comments.php, profile.php | Alta | Afecta a todo usuario que visite la página | Codificar la salida, sanear HTML permitido |
| 4 | Inyección HTML | dashboard.php, comments.php | Media | Engaño al usuario, alteración visual | Codificar la salida |
| 5 | Contraseñas con MD5 y débiles | bootstrap.php | Alta | Recuperación de credenciales | `password_hash` / `password_verify` y política de claves |
| 6 | Sin límite de intentos y enumeración | index.php, reset.php | Alta | Toma de cuentas | Mensaje genérico, límite de intentos, MFA |
| 7 | Fijación y mal cierre de sesión | bootstrap.php, index.php, logout.php | Alta | Secuestro de sesión | `session_regenerate_id(true)`, HttpOnly, SameSite, `session_destroy` |
| 8 | Recuperación de clave insegura | reset.php | Crítica | Toma de cualquier cuenta | `random_bytes`, expiración, un solo uso, envío fuera de banda |
| 9 | Rol en cookie | admin.php | Crítica | Escalamiento de privilegios | Validar el rol en el servidor |
| 10 | IDOR | order.php, profile.php | Alta | Lectura o cambio de datos ajenos | Verificar la propiedad del recurso |
| 11 | Asignación masiva | register.php | Crítica | Creación de administradores o saldo ficticio | Lista blanca de campos |
| 12 | Validación de parámetros | register.php, comments.php, transfer.php | Media | Datos corruptos y abuso | Validar tipo, rango, formato y longitud en el servidor |
| 13 | Redirección abierta | index.php | Media | Apoyo a campañas de phishing | Lista blanca de destinos |
| 14 | Lógica de transferencias | transfer.php | Crítica | Pérdidas económicas directas | Montos positivos, saldo suficiente, transacciones |
| 15 | Lógica de compra y cupones | shop.php | Crítica | Fraude en precios y descuentos | Precio desde el servidor, cupón de un solo uso |
| 16 | CSRF | comments.php, profile.php, transfer.php | Alta | Acciones sin consentimiento | Token anti-CSRF, solo POST, SameSite |
| 17 | Exposición de información | index.php, search.php, register.php, admin.php | Media | Facilita ataques posteriores | Errores genéricos, registro interno, no mostrar hashes |

## Ruta de trabajo sugerida
1. **Reconocimiento:** el participante recorre los módulos e identifica cada punto de entrada.
2. **Evidencia:** documenta el hallazgo, su causa en el código y su impacto.
3. **Remediación:** corrige en una rama aparte, priorizando las severidades críticas.
4. **Validación:** repite la prueba y confirma que la funcionalidad legítima sigue operando.

Los comentarios `VULN-` en el código señalan cada falla. Para un ejercicio a ciegas conviene entregar una copia sin esos comentarios y sin la matriz.

Las severidades son orientativas y corresponden al contexto del laboratorio.
