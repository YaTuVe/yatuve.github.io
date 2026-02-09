// proxy-config.js - Proxy simple con autenticación
const http = require('http');

// Usuarios autorizados
const users = {
    'admin': 'admin123',
    'user1': 'pass123',
    'user2': 'pass456'
};

const server = http.createServer((req, res) => {
    console.log(`[${new Date().toISOString()}] ${req.method} ${req.url}`);
    
    // Verificar autenticación
    const auth = req.headers['proxy-authorization'];
    
    if (!auth) {
        res.writeHead(407, {
            'Proxy-Authenticate': 'Basic realm="YaTuVe Proxy"'
        });
        res.end('Authentication required');
        return;
    }
    
    // Extraer credenciales
    const authType = auth.split(' ')[0];
    if (authType !== 'Basic') {
        res.writeHead(407, { 'Proxy-Authenticate': 'Basic realm="YaTuVe Proxy"' });
        res.end('Basic auth required');
        return;
    }
    
    const credentials = Buffer.from(auth.split(' ')[1], 'base64').toString();
    const [username, password] = credentials.split(':');
    
    // Verificar usuario
    if (!users[username] || users[username] !== password) {
        res.writeHead(407, { 'Proxy-Authenticate': 'Basic realm="YaTuVe Proxy"' });
        res.end('Invalid credentials');
        return;
    }
    
    console.log(`✅ Authenticated: ${username}`);
    
    // Respuesta exitosa
    res.writeHead(200, { 'Content-Type': 'text/html' });
    res.end(`
        <html>
        <body style="background: #0f0f23; color: white; padding: 20px;">
            <h1>✅ YaTuVe Proxy Active</h1>
            <p>User: ${username}</p>
            <p>Time: ${new Date().toLocaleString()}</p>
            <p>Status: Connected</p>
        </body>
        </html>
    `);
});

server.listen(8080, () => {
    console.log('YaTuVe Proxy running on port 8080');
    console.log('Users:', Object.keys(users));
});