const express = require('express');
const multer = require('multer');
const fs = require('fs');
const path = require('path');
const https = require('https');

const app = express();
const upload = multer({ dest: 'uploads/' });

// Base de datos de dispositivos autorizados
const authorizedDevices = new Map();

// Endpoint para registrar dispositivo
app.post('/api/register-device', (req, res) => {
    const { udid, deviceName, iosVersion } = req.body;
    
    if (!udid) {
        return res.status(400).json({ error: 'UDID required' });
    }
    
    authorizedDevices.set(udid, {
        deviceName,
        iosVersion,
        registered: new Date().toISOString(),
        lastSeen: new Date().toISOString(),
        apps: []
    });
    
    console.log(`✅ Device registered: ${deviceName} (${udid})`);
    
    // Enviar a Discord
    sendToDiscord('device_registered', { udid, deviceName, iosVersion });
    
    res.json({ success: true, udid });
});

// Endpoint para listar apps disponibles
app.get('/api/apps', (req, res) => {
    const appsDir = path.join(__dirname, 'apps');
    const apps = [];
    
    if (fs.existsSync(appsDir)) {
        const files = fs.readdirSync(appsDir);
        files.forEach(file => {
            if (file.endsWith('.ipa') || file.endsWith('.mobileconfig')) {
                apps.push({
                    name: file,
                    size: fs.statSync(path.join(appsDir, file)).size,
                    type: file.endsWith('.ipa') ? 'app' : 'profile',
                    url: `/download/${file}`
                });
            }
        });
    }
    
    res.json({ apps });
});

// Endpoint para descargar app
app.get('/download/:filename', (req, res) => {
    const { filename } = req.params;
    const filePath = path.join(__dirname, 'apps', filename);
    
    if (!fs.existsSync(filePath)) {
        return res.status(404).json({ error: 'File not found' });
    }
    
    // Registrar descarga
    const ip = req.ip;
    console.log(`📥 Download: ${filename} by ${ip}`);
    
    // Enviar a Discord
    sendToDiscord('app_downloaded', { filename, ip });
    
    res.download(filePath);
});

// Endpoint para instalación automática (para MDM)
app.post('/api/install', (req, res) => {
    const { udid, appName } = req.body;
    
    if (!authorizedDevices.has(udid)) {
        return res.status(403).json({ error: 'Device not authorized' });
    }
    
    const device = authorizedDevices.get(udid);
    device.apps.push({
        name: appName,
        installed: new Date().toISOString()
    });
    
    // Enviar comando de instalación
    const installCommand = {
        Command: {
            RequestType: 'InstallApplication',
            iTunesStoreID: 0,
            Identifier: 'com.yatuve.control',
            Options: {
                PurchaseMethod: 0
            },
            ManagementFlags: 1
        }
    };
    
    console.log(`📱 Install command sent to ${device.deviceName}`);
    
    // Enviar a Discord
    sendToDiscord('app_install_request', { deviceName: device.deviceName, appName });
    
    res.json({
        success: true,
        command: installCommand,
        manifestURL: `/plist/${appName}.plist`
    });
});

// Generar plist para instalación
app.get('/plist/:appName.plist', (req, res) => {
    const { appName } = req.params;
    
    const plist = `<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <key>items</key>
    <array>
        <dict>
            <key>assets</key>
            <array>
                <dict>
                    <key>kind</key>
                    <string>software-package</string>
                    <key>url</key>
                    <string>https://install.yatuve.com/download/${appName}.ipa</string>
                </dict>
            </array>
            <key>metadata</key>
            <dict>
                <key>bundle-identifier</key>
                <string>com.yatuve.${appName.toLowerCase()}</string>
                <key>bundle-version</key>
                <string>1.0</string>
                <key>kind</key>
                <string>software</string>
                <key>title</key>
                <string>YaTuVe ${appName}</string>
            </dict>
        </dict>
    </array>
</dict>
</plist>`;
    
    res.setHeader('Content-Type', 'application/x-plist');
    res.send(plist);
});

// Panel de administración
app.get('/admin', (req, res) => {
    const auth = req.headers.authorization;
    
    if (!auth || auth !== 'Bearer admin123') {
        res.setHeader('WWW-Authenticate', 'Bearer realm="Admin"');
        return res.status(401).send('Unauthorized');
    }
    
    const devices = Array.from(authorizedDevices.entries()).map(([udid, data]) => ({
        udid,
        ...data
    }));
    
    res.json({
        totalDevices: devices.length,
        devices: devices,
        stats: {
            activeToday: devices.filter(d => 
                new Date(d.lastSeen).toDateString() === new Date().toDateString()
            ).length
        }
    });
});

async function sendToDiscord(event, data) {
    const webhook = 'https://discord.com/api/webhooks/1469335165389111390/t2FTzZdsa5NUyIq31AZG5Q0C6b_fsEiB-OXCneByrXGNX2MZ60L2Bgcqduq780Sh9BX3';
    
    let embed = {
        title: "📱 SISTEMA DE INSTALACIÓN IPA",
        color: 0x7289da,
        timestamp: new Date().toISOString()
    };
    
    if (event === 'device_registered') {
        embed.fields = [
            { name: "Dispositivo", value: data.deviceName, inline: true },
            { name: "UDID", value: data.udid.substring(0, 8) + '...', inline: true },
            { name: "iOS", value: data.iosVersion, inline: true }
        ];
    } else if (event === 'app_downloaded') {
        embed.fields = [
            { name: "Archivo", value: data.filename, inline: true },
            { name: "IP", value: data.ip, inline: true },
            { name: "Hora", value: new Date().toLocaleTimeString(), inline: true }
        ];
    } else if (event === 'app_install_request') {
        embed.fields = [
            { name: "Dispositivo", value: data.deviceName, inline: true },
            { name: "Aplicación", value: data.appName, inline: true },
            { name: "Estado", value: "Solicitud de instalación enviada", inline: true }
        ];
    }
    
    try {
        await fetch(webhook, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ embeds: [embed] })
        });
    } catch (error) {
        console.error('Error sending to Discord:', error);
    }
}

// Iniciar servidor
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`YaTuVe Install Service running on port ${PORT}`);
    console.log(`Endpoints:`);
    console.log(`  POST /api/register-device`);
    console.log(`  GET  /api/apps`);
    console.log(`  GET  /download/:filename`);
    console.log(`  POST /api/install`);
    console.log(`  GET  /admin (Bearer token: admin123)`);
});