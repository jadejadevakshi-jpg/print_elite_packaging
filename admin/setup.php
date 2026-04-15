<?php
declare(strict_types=1);

require_once __DIR__ . DIRECTORY_SEPARATOR . '_bootstrap.php';

function run_sql_file(PDO $pdo, string $path): array
{
    $sql = file_get_contents($path);
    if ($sql === false) return ['ok' => false, 'error' => 'Could not read SQL file'];

    $sql = preg_replace('/^\xEF\xBB\xBF/', '', $sql);
    $lines = preg_split("/\r\n|\n|\r/", $sql);
    $statements = [];
    $buffer = '';

    foreach ($lines as $line) {
        $trim = trim($line);
        if ($trim === '' || substr($trim, 0, 2) === '--') continue;
        $buffer .= $line . "\n";
        if (preg_match('/;\s*$/', trim($buffer))) {
            $statements[] = $buffer;
            $buffer = '';
        }
    }
    if (trim($buffer) !== '') $statements[] = $buffer;

    try {
        foreach ($statements as $stmt) {
            $pdo->exec($stmt);
        }
    } catch (Throwable $e) {
        return ['ok' => false, 'error' => $e->getMessage()];
    }

    return ['ok' => true, 'count' => count($statements)];
}

$cfgPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';
$cfg = require $cfgPath;

// Connect without db first (so we can create/import)
$dsn = sprintf(
    "mysql:host=%s;port=%d;charset=%s",
    $cfg['host'] ?? '127.0.0.1',
    (int)($cfg['port'] ?? 3306),
    $cfg['charset'] ?? 'utf8mb4'
);

try {
    $pdo = new PDO($dsn, (string)($cfg['user'] ?? 'root'), (string)($cfg['pass'] ?? ''), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo "<h2>DB connection failed</h2><pre>" . h($e->getMessage()) . "</pre>";
    exit;
}

$sqlPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'elite_print.sql';
$import = run_sql_file($pdo, $sqlPath);

// Now connect to the app db
try {
    $pdo2 = db();
} catch (Throwable $e) {
    http_response_code(500);
    echo "<h2>DB (elite_print) connection failed</h2><pre>" . h($e->getMessage()) . "</pre>";
    exit;
}

// Create admin user if missing
$adminEmail = 'admin@eliteprint.local';
$adminPass = 'Admin@12345';
$adminName = 'Elite Print Admin';

$stmt = $pdo2->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
$stmt->execute([':email' => $adminEmail]);
$row = $stmt->fetch();

if (!$row) {
    $hash = password_hash($adminPass, PASSWORD_BCRYPT);
    $ins = $pdo2->prepare("INSERT INTO users (email, password_hash, name, role, is_active) VALUES (:e, :h, :n, 'admin', 1)");
    $ins->execute([':e' => $adminEmail, ':h' => $hash, ':n' => $adminName]);
    $created = true;
} else {
    $created = false;
}

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Elite Print Setup</title>
    <style>
      body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;margin:24px;max-width:920px}
      code,pre{background:#f6f6f6;padding:2px 6px;border-radius:6px}
      .card{border:1px solid #ddd;border-radius:10px;padding:16px;margin:14px 0}
      .ok{color:#0a7a2f;font-weight:700}
      .bad{color:#b00020;font-weight:700}
    </style>
  </head>
  <body>
    <h1>Elite Print — Setup</h1>
    <div class="card">
      <p><strong>Schema import</strong>:
        <?php if (($import['ok'] ?? false) === true): ?>
          <span class="ok">OK</span> (executed <?php echo (int)($import['count'] ?? 0); ?> statements)
        <?php else: ?>
          <span class="bad">FAILED</span>
          <pre><?php echo h((string)($import['error'] ?? 'Unknown error')); ?></pre>
        <?php endif; ?>
      </p>
      <p><strong>Admin user</strong>:
        <?php if ($created): ?>
          <span class="ok">CREATED</span>
        <?php else: ?>
          <span class="ok">ALREADY EXISTS</span>
        <?php endif; ?>
      </p>
      <p>
        Login email: <code><?php echo h($adminEmail); ?></code><br />
        Login password: <code><?php echo h($adminPass); ?></code>
      </p>
      <p><a href="/project/admin/">Go to Admin</a></p>
    </div>
    <div class="card">
      <p><strong>Important</strong>: Change the admin password after first login.</p>
      <p>If your project folder name is not <code>project</code>, update links in this page and in <code>admin/_bootstrap.php</code>.</p>
    </div>
  </body>
</html>

