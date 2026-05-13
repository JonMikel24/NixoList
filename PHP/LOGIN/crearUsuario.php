<?php
require_once("../conexion.php");

if (!isset($conexion) || $conexion->connect_error) {
    die("Error de conexión a la base de datos");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Recibir y limpiar datos
    $email      = trim($_POST["email"]);
    $usuario    = trim($_POST["usuario"]);
    $contrasena = $_POST["contrasena"];

    // --- VALIDACIONES DE SERVIDOR (Doble seguridad) ---

    // A. Validar Usuario (mín 3 caracteres y solo letras/números)
    if (strlen($usuario) < 3 || !preg_match('/^[a-zA-Z0-9]+$/', $usuario)) {
        header("Location: registrarse.php?error=formato_usuario"); 
        exit;
    }

    // B. Validar Email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: registrarse.php?error=formato_email");
        exit;
    }

    // C. Validar Contraseña (mín 6, 1 mayúscula, 1 número)
    if (strlen($contrasena) < 6 || !preg_match('/[A-Z]/', $contrasena) || !preg_match('/[0-9]/', $contrasena)) {
        header("Location: registrarse.php?error=formato_contra");
        exit;
    }

    // --- COMPROBAR REPETIDOS ---

    // 1. Email repetido
    $stmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        header("Location: registrarse.php?error=email");
        exit;
    }
    $stmt->close();

    // 2. Usuario repetido
    $stmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE username = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        header("Location: registrarse.php?error=usuario");
        exit;
    }
    $stmt->close();

    // --- INSERTAR ---

    $passwordHash = password_hash($contrasena, PASSWORD_DEFAULT);
    $sql = "INSERT INTO usuarios (email, username, password_hash) VALUES (?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sss", $email, $usuario, $passwordHash);

    if ($stmt->execute()) {
        echo "<script>
                alert('Usuario registrado correctamente');
                window.location.href = 'index.php';
              </script>";
    } else {
        echo "Error al registrar usuario: " . $stmt->error;
    }

    $stmt->close();
}

$conexion->close();
?>