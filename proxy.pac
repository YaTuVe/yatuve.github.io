function FindProxyForURL(url, host) {
    // Juegos a modificar
    var games = [
        "freefire",
        "ff.garena",
        "com.dts.freefireth",
        "com.dts.freefiremax"
    ];
    
    // URLs del sistema YaTuVe
    var yatuveUrls = [
        "yatuve.github.io",
        "yaddielk44.com",
        "proxy.yaddielk44.com"
    ];
    
    // Si es juego, usar proxy para modificación
    for(var i = 0; i < games.length; i++) {
        if (host.indexOf(games[i]) != -1) {
            return "PROXY proxy.yaddielk44.com:8443; DIRECT";
        }
    }
    
    // Si es URL de YaTuVe, usar proxy
    for(var j = 0; j < yatuveUrls.length; j++) {
        if (host == yatuveUrls[j] || host.endsWith('.' + yatuveUrls[j])) {
            return "PROXY proxy.yaddielk44.com:8443; DIRECT";
        }
    }
    
    // Para todo lo demás, conexión directa
    return "DIRECT";
}