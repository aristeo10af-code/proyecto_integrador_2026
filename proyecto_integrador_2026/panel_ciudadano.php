<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'ciudadano') header("Location: index.php");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Panel Ciudadano</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Hola, <?php echo $_SESSION['nombre']; ?></h1>
        <div class="card" style="width: 100%;">
            <h3>Estado del Camión</h3>
            <div id="info-camion" style="font-size: 20px; color: green; font-weight: bold;">
                Buscando camión más cercano...
            </div>
            <p>Mantén esta ventana abierta para recibir la alerta de 500m.</p>
            <a href="logout.php">Cerrar Sesión</a>
        </div>
    </div>

    <script>
    function monitorearCamion() {
        setInterval(() => {
            fetch('get_camion.php')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('info-camion').innerText = 
                        `El camión está en: ${data.lat}, ${data.lng}`;
                    
                    // Aquí podrías añadir la lógica de distancia (Haversine)
                });
        }, 5000); // Consultar cada 5 segundos
    }
    monitorearCamion();
    </script>
</body>
</html>