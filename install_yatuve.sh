#!/bin/bash
# install_yatuve.sh

echo "Instalando YaTuVe System..."
echo ""

# 1. Instalar perfil
echo "Descargando perfil de configuración..."
curl -O https://yatuve.github.io/yaddiel-profile.mobileconfig

echo "Abre yaddiel-profile.mobileconfig en tu iPhone para instalar"
echo ""

# 2. Instalar tweak (requiere jailbreak)
echo "Para jailbroken devices:"
echo "1. Copia YATUVE.deb a /var/root/Media/Cydia/AutoInstall/"
echo "2. Reinicia SpringBoard"
echo "3. El tweak se instalará automáticamente"
echo ""

# 3. Configurar DNS manualmente
echo "Configuración manual DNS:"
echo "1. Ve a Ajustes > Wi-Fi"
echo "2. Toca la 'i' junto a tu red"
echo "3. Configurar DNS > Manual"
echo "4. Añade: 1.0.0.1, 8.8.4.4"
echo ""

echo "Instalación completada!"
echo "Abre tu juego y verás el botón rojo 'Y'"