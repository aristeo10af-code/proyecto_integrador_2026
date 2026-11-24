<?php
include 'config.php';
session_start();

// REGISTRO
if (isset($_POST['registro'])) {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $rol = $_POST['rol'];

    $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES ('$nombre', '$email', '$pass', '$rol')";
    if ($conn->query($sql)) {
        echo "<script>alert('Registro exitoso'); window.location='index.php';</script>";
    }
}

// LOGIN
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $result = $conn->query("SELECT * FROM usuarios WHERE email='$email'");
    $user = $result->fetch_assoc();

    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user_id'] = $user['id_usuario'];
        $_SESSION['rol'] = $user['rol'];
        $_SESSION['nombre'] = $user['nombre'];

        if ($user['rol'] == 'recolector') {
            header("Location: panel_chofer.php");
        } else {
            header("Location: panel_ciudadano.php");
        }
    } else {
        echo "<script>alert('Error: Datos incorrectos'); window.location='index.php';</script>";
    }
}
?>