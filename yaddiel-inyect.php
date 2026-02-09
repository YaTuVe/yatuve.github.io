<?php
// Verificar autenticación
session_start();
if (!isset($_GET['user']) && !isset($_SESSION['yaddiel_user'])) {
    header('Location: index.html');
    exit;
}

$username = isset($_GET['user']) ? $_GET['user'] : $_SESSION['yaddiel_user'];
$_SESSION['yaddiel_user'] = $username;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YADDIEL INYECT - Panel de Control</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: #000000;
            color: #ffffff;
            overflow: hidden;
            height: 100vh;
        }
        
        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: 1;
        }
        
        .main-title {
            position: absolute;
            top: 20px;
            left: 0;
            width: 100%;
            text-align: center;
            z-index: 2;
            color: #ff0000;
            font-size: 3rem;
            text-shadow: 0 0 20px rgba(255, 0, 0, 0.7);
            letter-spacing: 3px;
        }
        
        .user-info {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 2;
            background: rgba(255, 0, 0, 0.2);
            padding: 10px 20px;
            border-radius: 10px;
            border: 1px solid rgba(255, 0, 0, 0.5);
        }
        
        .hack-menu {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 3;
            background: rgba(0, 0, 0, 0.85);
            border: 2px solid #ff0000;
            border-radius: 15px;
            padding: 30px;
            width: 400px;
            box-shadow: 0 0 50px rgba(255, 0, 0, 0.5);
            backdrop-filter: blur(10px);
        }
        
        .hack-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .hack-item:last-child {
            border-bottom: none;
        }
        
        .hack-name {
            color: #ff9999;
            font-size: 1.2rem;
        }
        
        /* Switch estilo moderno */
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
        }
        
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #333;
            transition: .4s;
            border-radius: 34px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: #ff0000;
        }
        
        input:checked + .slider:before {
            transform: translateX(30px);
        }
        
        .status-indicator {
            position: absolute;
            bottom: 20px;
            left: 20px;
            z-index: 2;
            background: rgba(0, 0, 0, 0.7);
            padding: 10px 20px;
            border-radius: 10px;
            border-left: 5px solid #ff0000;
        }
        
        .status-item {
            margin: 5px 0;
            color: #0f0;
            font-family: monospace;
        }
        
        .status-item.off {
            color: #f00;
        }
        
        .logout-btn {
            position: absolute;
            bottom: 20px;
            right: 20px;
            z-index: 2;
            background: rgba(255, 0, 0, 0.3);
            color: white;
            border: 1px solid #ff0000;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            background: rgba(255, 0, 0, 0.5);
            transform: translateY(-2px);
        }
        
        .menu-title {
            text-align: center;
            color: #ff0000;
            margin-bottom: 25px;
            font-size: 1.8rem;
            text-shadow: 0 0 10px rgba(255, 0, 0, 0.5);
        }
    </style>
</head>
<body>
    <!-- Partículas rojas de fondo -->
    <div id="particles-js"></div>
    
    <!-- Título principal -->
    <h1 class="main-title">YADDIEL INYECT</h1>
    
    <!-- Información de usuario -->
    <div class="user-info">
        Usuario: <strong><?php echo htmlspecialchars($username); ?></strong><br>
        IP: <?php echo $_SERVER['REMOTE_ADDR']; ?>
    </div>
    
    <!-- Menú de hacks -->
    <div class="hack-menu">
        <h2 class="menu-title">PANEL DE HACKS</h2>
        
        <div class="hack-item">
            <span class="hack-name">Holograma Armas</span>
            <label class="switch">
                <input type="checkbox" id="hologram-weapons">
                <span class="slider"></span>
            </label>
        </div>
        
        <div class="hack-item">
            <span class="hack-name">Holograma Personas</span>
            <label class="switch">
                <input type="checkbox" id="hologram-people">
                <span class="slider"></span>
            </label>
        </div>
        
        <div class="hack-item">
            <span class="hack-name">Aimbot</span>
            <label class="switch">
                <input type="checkbox" id="aimbot">
                <span class="slider"></span>
            </label>
        </div>
        
        <div class="hack-item">
            <span class="hack-name">WallHack</span>
            <label class="switch">
                <input type="checkbox" id="wallhack">
                <span class="slider"></span>
            </label>
        </div>
        
        <div class="hack-item">
            <span class="hack-name">Aim Fov</span>
            <label class="switch">
                <input type="checkbox" id="aim-fov">
                <span class="slider"></span>
            </label>
        </div>
        
        <div class="hack-item">
            <span class="hack-name">Aim Silent</span>
            <label class="switch">
                <input type="checkbox" id="aim-silent">
                <span class="slider"></span>
            </label>
        </div>
        
        <div class="hack-item">
            <span class="hack-name">Speed</span>
            <label class="switch">
                <input type="checkbox" id="speed">
                <span class="slider"></span>
            </label>
        </div>
    </div>
    
    <!-- Indicador de estado -->
    <div class="status-indicator" id="statusIndicator">
        <div class="status-item" id="statusHologramWeapons">Holograma Armas: OFF</div>
        <div class="status-item" id="statusHologramPeople">Holograma Personas: OFF</div>
        <div class="status-item" id="statusAimbot">Aimbot: OFF</div>
        <div class="status-item" id="statusWallhack">WallHack: OFF</div>
        <div class="status-item" id="statusAimFov">Aim Fov: OFF</div>
        <div class="status-item" id="statusAimSilent">Aim Silent: OFF</div>
        <div class="status-item" id="statusSpeed">Speed: OFF</div>
    </div>
    
    <!-- Botón de logout -->
    <button class="logout-btn" onclick="logout()">CERRAR SESIÓN</button>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        // Configuración de partículas rojas intensas
        particlesJS('particles-js', {
            particles: {
                number: { value: 200, density: { enable: true, value_area: 800 } },
                color: { value: "#ff0000" },
                shape: { type: "circle" },
                opacity: { value: 0.7, random: true, anim: { enable: true, speed: 1, opacity_min: 0.1 } },
                size: { value: 4, random: true, anim: { enable: true, speed: 2, size_min: 0.1 } },
                line_linked: {
                    enable: true,
                    distance: 150,
                    color: "#ff3333",
                    opacity: 0.4,
                    width: 1
                },
                move: {
                    enable: true,
                    speed: 5,
                    direction: "none",
                    random: true,
                    straight: false,
                    out_mode: "out",
                    bounce: false,
                    attract: { enable: false }
                }
            },
            interactivity: {
                detect_on: "canvas",
                events: {
                    onhover: { enable: true, mode: "repulse" },
                    onclick: { enable: true, mode: "push" },
                    resize: true
                },
                modes: {
                    repulse: { distance: 100, duration: 0.4 },
                    push: { particles_nb: 6 }
                }
            },
            retina_detect: true
        });
        
        // Controladores de switches
        const hacks = {
            'hologram-weapons': 'statusHologramWeapons',
            'hologram-people': 'statusHologramPeople',
            'aimbot': 'statusAimbot',
            'wallhack': 'statusWallhack',
            'aim-fov': 'statusAimFov',
            'aim-silent': 'statusAimSilent',
            'speed': 'statusSpeed'
        };
        
        // Inicializar todos los switches
        Object.keys(hacks).forEach(hackId => {
            const checkbox = document.getElementById(hackId);
            const statusElement = document.getElementById(hacks[hackId]);
            
            // Cargar estado guardado
            const savedState = localStorage.getItem(`yaddiel_${hackId}`);
            if (savedState === 'true') {
                checkbox.checked = true;
                statusElement.textContent = statusElement.textContent.replace('OFF', 'ON');
                statusElement.classList.remove('off');
            } else {
                statusElement.classList.add('off');
            }
            
            // Agregar event listener
            checkbox.addEventListener('change', function() {
                const isOn = this.checked;
                const hackName = this.parentElement.previousElementSibling.textContent;
                
                // Actualizar estado visual
                if (isOn) {
                    statusElement.textContent = statusElement.textContent.replace('OFF', 'ON');
                    statusElement.classList.remove('off');
                    console.log(`✅ ${hackName} ACTIVADO`);
                } else {
                    statusElement.textContent = statusElement.textContent.replace('ON', 'OFF');
                    statusElement.classList.add('off');
                    console.log(`❌ ${hackName} DESACTIVADO`);
                }
                
                // Guardar estado
                localStorage.setItem(`yaddiel_${hackId}`, isOn);
                
                // Enviar comando al servidor (simulado)
                sendCommand(hackId, isOn);
            });
        });
        
        // Función para enviar comandos (simulada)
        function sendCommand(hack, state) {
            const commands = {
                'hologram-weapons': { code: 0x01, value: state ? 1 : 0 },
                'hologram-people': { code: 0x02, value: state ? 1 : 0 },
                'aimbot': { code: 0x03, value: state ? 1 : 0 },
                'wallhack': { code: 0x04, value: state ? 1 : 0 },
                'aim-fov': { code: 0x05, value: state ? 1 : 0 },
                'aim-silent': { code: 0x06, value: state ? 1 : 0 },
                'speed': { code: 0x07, value: state ? 1 : 0 }
            };
            
            const cmd = commands[hack];
            console.log(`🔧 Enviando comando: Hack=${hack}, Código=0x${cmd.code.toString(16)}, Estado=${state}`);
            
            // Aquí iría la conexión real al servidor/proxy
            // fetch(`/api/hack?code=${cmd.code}&value=${cmd.value}`);
            
            // Mostrar notificación visual
            showNotification(`${hack.replace('-', ' ').toUpperCase()} ${state ? 'ACTIVADO' : 'DESACTIVADO'}`);
        }
        
        // Función para mostrar notificaciones
        function showNotification(message) {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 100px;
                right: 20px;
                background: rgba(255, 0, 0, 0.8);
                color: white;
                padding: 15px 25px;
                border-radius: 10px;
                z-index: 1000;
                animation: slideIn 0.3s, fadeOut 0.3s 2.7s;
                border: 1px solid #ff0000;
                box-shadow: 0 0 20px rgba(255, 0, 0, 0.5);
            `;
            
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
        
        // Logout
        function logout() {
            localStorage.clear();
            sessionStorage.clear();
            window.location.href = 'index.html';
        }
        
        // Inyectar CSS para animaciones
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes fadeOut {
                from { opacity: 1; }
                to { opacity: 0; }
            }
        `;
        document.head.appendChild(style);
        
        // Registrar inicio de sesión
        console.log('🚀 Yaddiel Inyect iniciado');
        console.log('👤 Usuario: <?php echo $username; ?>');
        console.log('🌐 IP: <?php echo $_SERVER['REMOTE_ADDR']; ?>');
        console.log('🎮 Panel de hacks cargado');
        
        // Mostrar notificación de bienvenida
        setTimeout(() => {
            showNotification('YADDIEL INYECT INICIADO • BIENVENIDO');
        }, 1000);
    </script>
</body>
</html>