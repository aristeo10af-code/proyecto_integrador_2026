let map, marker, polyline, puntosRuta = [];
let idUsuario, rolActual, transmitiendo = false;
const casaUsuario = [19.4326, -99.1332]; // Ubicación ejemplo

async function procesarLogin() {
    const user = document.getElementById('user').value;
    const pass = document.getElementById('pass').value;
    const errorMsg = document.getElementById('login-error');

    let fd = new FormData();
    fd.append('user', user);
    fd.append('pass', pass);

    const resp = await fetch('api.php?action=login', { method: 'POST', body: fd });
    const data = await resp.json();

    if (data.status === 'success') {
        idUsuario = data.id;
        rolActual = data.rol;
        document.getElementById('login-screen').classList.add('hidden');
        document.getElementById('lbl-rol').innerText = `| ${rolActual.toUpperCase()}`;
        configurarApp();
    } else {
        errorMsg.innerText = data.message;
    }
}

function configurarApp() {
    map = L.map('map').setView(casaUsuario, 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    
    marker = L.marker(casaUsuario, {
        icon: L.icon({iconUrl: 'https://cdn-icons-png.flaticon.com/512/2347/2347910.png', iconSize:[35,35]})
    }).addTo(map);
    
    polyline = L.polyline([], {color: '#1976d2', weight: 5}).addTo(map);

    if(rolActual === 'chofer') {
        document.getElementById('panel-chofer').classList.remove('hidden');
    } else {
        document.getElementById('panel-usuario').classList.remove('hidden');
        L.circle(casaUsuario, {radius: 500, color: 'green', fillOpacity: 0.1}).addTo(map);
        setInterval(escucharServidor, 3000);
    }
}

function toggleGPS() {
    transmitiendo = !transmitiendo;
    const btn = document.getElementById('btn-gps');
    btn.innerText = transmitiendo ? "🛑 Detener Ruta" : "▶️ Iniciar Recorrido";
    btn.style.background = transmitiendo ? "#d32f2f" : "#2e7d32";
    if(transmitiendo) enviarUbicacion();
}

function enviarUbicacion() {
    if(!transmitiendo) return;
    
    // Simular avance
    let lat = casaUsuario[0] + (puntosRuta.length * 0.0004);
    let lng = casaUsuario[1] + (puntosRuta.length * 0.0004);
    let pos = [lat, lng];
    
    puntosRuta.push(pos);
    marker.setLatLng(pos);
    polyline.setLatLngs(puntosRuta);
    map.panTo(pos);

    let fd = new FormData();
    fd.append('id_chofer', idUsuario);
    fd.append('lat', lat);
    fd.append('lng', lng);
    fd.append('puntos', JSON.stringify(puntosRuta));

    fetch('api.php?action=actualizar', { method: 'POST', body: fd });
    setTimeout(enviarUbicacion, 3000);
}

function escucharServidor() {
    fetch('api.php?action=obtener')
    .then(r => r.json())
    .then(data => {
        if(data && data.puntos_json) {
            const pts = JSON.parse(data.puntos_json);
            marker.setLatLng([data.latitud, data.longitud]);
            polyline.setLatLngs(pts);
            
            let dist = map.distance(casaUsuario, [data.latitud, data.longitud]);
            document.getElementById('distancia-info').innerText = `Distancia: ${Math.round(dist)} metros.`;
            
            if(dist < 500) {
                document.getElementById('alerta-proximidad').innerHTML = "🔔 ¡EL CAMIÓN ESTÁ CERCA!";
                document.getElementById('status-dot').style.background = "#4caf50";
            }
        }
    });
}