<?php
// proxy-auth.php - Proxy con autenticación básica

// Configuración del proxy
$PROXY_HOST = 'proxy.yaddielk44.com';
$PROXY_PORT = 8443;
$PROXY_USER = 'admin';
$PROXY_PASS = 'admin123';

// Verificar autenticación
function checkAuth() {
    if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
        header('WWW-Authenticate: Basic realm="Yaddiel Inyect Proxy"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Autenticación requerida';
        exit;
    }
    
    $username = $_SERVER['PHP_AUTH_USER'];
    $password = $_SERVER['PHP_AUTH_PW'];
    
    // Validar credenciales (en producción usaría base de datos)
    $validUsers = [
        'admin' => 'admin123',
        'yaddiel' => '123456',
        'user' => 'pass123'
    ];
    
    if (!isset($validUsers[$username]) || $validUsers[$username] !== $password) {
        header('HTTP/1.0 403 Forbidden');
        echo 'Acceso denegado';
        exit;
    }
    
    return $username;
}

// Registrar actividad
function logActivity($username, $action) {
    $log = date('Y-m-d H:i:s') . " | User: $username | Action: $action | IP: " . $_SERVER['REMOTE_ADDR'] . PHP_EOL;
    file_put_contents('proxy-access.log', $log, FILE_APPEND);
    
    // Enviar a Discord si está configurado
    $webhook = 'https://discord.com/api/webhooks/1469335165389111390/t2FTzZdsa5NUyIq31AZG5Q0C6b_fsEiB-OXCneByrXGNX2MZ60L2Bgcqduq780Sh9BX3';
    $data = [
        'username' => 'Yaddiel Proxy',
        'content' => "🔌 **Proxy Activity**\nUser: `$username`\nAction: `$action`\nIP: `{$_SERVER['REMOTE_ADDR']}`"
    ];
    
    $options = [
        'http' => [
            'header'  => "Content-Type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
        ],
    ];
    
    @file_get_contents($webhook, false, stream_context_create($options));
}

// Main
$user = checkAuth();
$action = $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'];
logActivity($user, $action);

// Configurar proxy para conexiones salientes
putenv("http_proxy=http://$PROXY_USER:$PROXY_PASS@$PROXY_HOST:$PROXY_PORT");
putenv("https_proxy=http://$PROXY_USER:$PROXY_PASS@$PROXY_HOST:$PROXY_PORT");

// Verificar si es solicitud de descarga
if (isset($_GET['download']) && $_GET['download'] === 'yaddiel-inject') {
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="Yaddiel_Inject.bin"');
    header('Content-Length: ' . filesize('yaddiel-inject.bin'));
    readfile('yaddiel-inject.bin');
    exit;
}

// Servir página de proxy exitoso
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Yaddiel Proxy - Conectado</title>
    <style>
        body {
            background: #000;
            color: #0f0;
            font-family: monospace;
            padding: 20px;
        }
        .container {
            border: 2px solid #0f0;
            padding: 20px;
            margin: 20px auto;
            max-width: 800px;
        }
        .status {
            color: #0f0;
            font-size: 1.5em;
        }
        .info {
            color: #ff0;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>PROXY YADDIEL INYECT ACTIVO</h1>
        <div class="status">Usuario: <?php echo htmlspecialchars($user); ?></div>
        <div class="info">IP: <?php echo $_SERVER['REMOTE_ADDR']; ?></div>
        <div class="info">Hora: <?php echo date('Y-m-d H:i:s'); ?></div>
        <div class="info">Proxy Server: <?php echo "$PROXY_HOST:$PROXY_PORT"; ?></div>
        <div class="info">Estado: CONECTADO</div>
        <div class="info">DNS: 1.1.1.1, 8.8.8.8, 9.9.9.9</div>
        <div class="info">Bypass: ACTIVADO</div>
    </div>
</body>
</html>