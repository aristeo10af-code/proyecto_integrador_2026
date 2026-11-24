<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'recolector') header("Location: index.php");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Panel Chofer</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Modo Recolector: <?php echo $_SESSION['nombre']; ?></h1>
        <div class="card" style="width: 100%;">
            <p id="status">Presiona el botón para iniciar ruta</p>
            <button onclick="activarGPS()" class="btn-green">Activar Transmisión GPS</button>
            <a href="logout.php">Cerrar Sesión</a>
        </div>
    </div>

    <script>
    function activarGPS() {
        if (navigator.geolocation) {
            document.getElementById('status').innerText = "Transmitiendo ubicación en vivo...";
            navigator.geolocation.watchPosition(pos => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                
                // Enviar a un archivo PHP que actualice la BD
                fetch(`update_gps.php?lat=${lat}&lng=${lng}`);
            });
        }
    }
    </script>
</body>
</html>