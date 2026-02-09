#!/bin/bash
# install-script.sh - Script de instalación para Yaddiel Inyect

echo "=============================================="
echo "    YADDIEL INYECT - INSTALACIÓN AUTOMÁTICA   "
echo "=============================================="
echo ""

# Verificar root
if [ "$EUID" -ne 0 ]; then 
    echo "⚠️  Por favor ejecuta como root: sudo $0"
    exit 1
fi

# Configuración
INSTALL_DIR="/var/www/yaddiel-inyect"
PROXY_PORT="8443"
DNS_SERVERS="1.1.1.1 8.8.8.8 9.9.9.9"

echo "📁 Creando directorio de instalación..."
mkdir -p $INSTALL_DIR
cd $INSTALL_DIR

echo "📦 Instalando dependencias..."
apt-get update
apt-get install -y nginx php-fpm php-curl php-json php-mbstring php-xml

echo "🌐 Configurando Nginx..."
cat > /etc/nginx/sites-available/yaddiel-inyect << EOF
server {
    listen 80;
    server_name yaddiel-inyect.local;
    root $INSTALL_DIR;
    index index.html index.php;
    
    location / {
        try_files \$uri \$uri/ =404;
    }
    
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location /proxy {
        auth_basic "Yaddiel Proxy";
        auth_basic_user_file $INSTALL_DIR/.htpasswd;
        proxy_pass http://localhost:$PROXY_PORT;
    }
}
EOF

ln -sf /etc/nginx/sites-available/yaddiel-inyect /etc/nginx/sites-enabled/
nginx -t && systemctl reload nginx

echo "🔐 Configurando autenticación..."
echo "admin:\$(openssl passwd -crypt admin123)" > $INSTALL_DIR/.htpasswd
echo "yaddiel:\$(openssl passwd -crypt 123456)" >> $INSTALL_DIR/.htpasswd

echo "📄 Copiando archivos del sistema..."
# Aquí copiarías los archivos PHP/HTML desde tu repositorio
# cp /ruta/a/tus/archivos/* $INSTALL_DIR/

echo "⚙️ Configurando permisos..."
chown -R www-data:www-data $INSTALL_DIR
chmod 755 $INSTALL_DIR
chmod 644 $INSTALL_DIR/*.php
chmod 644 $INSTALL_DIR/*.html

echo "🔧 Configurando proxy local..."
cat > $INSTALL_DIR/proxy-server.js << 'EOF'
const http = require('http');
const net = require('net');

const users = {
    'admin': 'admin123',
    'yaddiel': '123456',
    'user': 'pass123'
};

const server = http.createServer((req, res) => {
    const auth = req.headers['proxy-authorization'];
    
    if (!auth) {
        res.writeHead(407, { 'Proxy-Authenticate': 'Basic realm="Yaddiel Proxy"' });
        res.end('Authentication required');
        return;
    }
    
    const credentials = Buffer.from(auth.split(' ')[1], 'base64').toString();
    const [username, password] = credentials.split(':');
    
    if (!users[username] || users[username] !== password) {
        res.writeHead(403);
        res.end('Invalid credentials');
        return;
    }
    
    console.log(`[PROXY] ${username} connected from ${req.socket.remoteAddress}`);
    
    res.writeHead(200, { 'Content-Type': 'text/html' });
    res.end(`
        <html><body style="background:#000;color:#0f0;">
        <h1>✅ YADDIEL PROXY ACTIVE</h1>
        <p>User: ${username}</p>
        <p>Status: CONNECTED</p>
        <p>Time: ${new Date().toLocaleString()}</p>
        </body></html>
    `);
});

server.listen(8443, () => {
    console.log('Yaddiel Proxy running on port 8443');
});
EOF

echo "📝 Creando servicio systemd para proxy..."
cat > /etc/systemd/system/yaddiel-proxy.service << EOF
[Unit]
Description=Yaddiel Inyect Proxy Server
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=$INSTALL_DIR
ExecStart=/usr/bin/node $INSTALL_DIR/proxy-server.js
Restart=always

[Install]
WantedBy=multi-user.target
EOF

systemctl daemon-reload
systemctl enable yaddiel-proxy
systemctl start yaddiel-proxy

echo "📊 Creando archivos de log..."
touch $INSTALL_DIR/access.log
touch $INSTALL_DIR/error.log
chown www-data:www-data $INSTALL_DIR/*.log

echo "✅ Instalación completada!"
echo ""
echo "=============================================="
echo "         INFORMACIÓN DE ACCESO                "
echo "=============================================="
echo "URL: http://$(hostname -I | awk '{print $1}')"
echo "Proxy: http://$(hostname -I | awk '{print $1}'):8443"
echo "Usuario admin: admin / admin123"
echo "Usuario yaddiel: yaddiel / 123456"
echo ""
echo "Para iniciar: http://tu-ip/"
echo "Para proxy: http://tu-ip:8443"
echo "=============================================="