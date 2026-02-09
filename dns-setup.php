<?php
// dns-setup.php - Configuración DNS personalizada

header('Content-Type: application/x-apple-aspen-config');
header('Content-Disposition: attachment; filename="Yaddiel-DNS.mobileconfig"');

$username = isset($_GET['user']) ? $_GET['user'] : 'yaddiel';
$device_id = isset($_GET['device']) ? $_GET['device'] : uniqid();

$xml = '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <key>PayloadContent</key>
    <array>
        <dict>
            <key>PayloadDescription</key>
            <string>DNS Configuration for Yaddiel Inyect</string>
            <key>PayloadDisplayName</key>
            <string>Yaddiel DNS</string>
            <key>PayloadIdentifier</key>
            <string>com.yaddiel.dns.' . $device_id . '</string>
            <key>PayloadType</key>
            <string>com.apple.dnsSettings.managed</string>
            <key>PayloadUUID</key>
            <string>' . md5($device_id) . '</string>
            <key>PayloadVersion</key>
            <integer>1</integer>
            <key>DNSSettings</key>
            <dict>
                <key>DNSProtocol</key>
                <string>HTTPS</string>
                <key>ServerAddresses</key>
                <array>
                    <string>1.1.1.1</string>
                    <string>8.8.8.8</string>
                    <string>9.9.9.9</string>
                </array>
                <key>ServerURL</key>
                <string>https://dns.yaddielk44.com/dns-query</string>
                <key>ServerName</key>
                <string>Yaddiel DNS</string>
            </dict>
        </dict>
        <dict>
            <key>PayloadDescription</key>
            <string>Proxy Configuration for Yaddiel Inyect</string>
            <key>PayloadDisplayName</key>
            <string>Yaddiel Proxy</string>
            <key>PayloadIdentifier</key>
            <string>com.yaddiel.proxy.' . $device_id . '</string>
            <key>PayloadType</key>
            <string>com.apple.proxy.http.managed</string>
            <key>PayloadUUID</key>
            <string>' . md5($device_id . 'proxy') . '</string>
            <key>PayloadVersion</key>
            <integer>1</integer>
            <key>ProxyServer</key>
            <string>proxy.yaddielk44.com</string>
            <key>ProxyServerPort</key>
            <integer>8443</integer>
            <key>ProxyType</key>
            <string>Manual</string>
            <key>ProxyUsername</key>
            <string>' . htmlspecialchars($username) . '</string>
            <key>ProxyPassword</key>
            <string>yaddiel2024</string>
            <key>ExceptionsList</key>
            <array>
                <string>*.apple.com</string>
                <string>*.icloud.com</string>
                <string>*.whatsapp.com</string>
                <string>*.google.com</string>
            </array>
        </dict>
    </array>
    <key>PayloadDescription</key>
    <string>Yaddiel Inyect Configuration - DNS & Proxy</string>
    <key>PayloadDisplayName</key>
    <string>Yaddiel Inyect Setup</string>
    <key>PayloadIdentifier</key>
    <string>com.yaddiel.profile.' . $device_id . '</string>
    <key>PayloadOrganization</key>
    <string>Yaddiel Systems</string>
    <key>PayloadRemovalDisallowed</key>
    <false/>
    <key>PayloadType</key>
    <string>Configuration</string>
    <key>PayloadUUID</key>
    <string>' . md5($device_id . 'main') . '</string>
    <key>PayloadVersion</key>
    <integer>1</integer>
</dict>
</plist>';

echo $xml;

// Registrar instalación
$log = [
    'timestamp' => date('Y-m-d H:i:s'),
    'username' => $username,
    'device_id' => $device_id,
    'ip' => $_SERVER['REMOTE_ADDR'],
    'action' => 'DNS Profile Downloaded'
];

file_put_contents('dns-installations.log', json_encode($log) . PHP_EOL, FILE_APPEND);
?>