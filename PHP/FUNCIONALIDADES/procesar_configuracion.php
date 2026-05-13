<?php
session_start();
require_once '../conexion.php'; 

if (!isset($_SESSION['Usuario'])) exit("Acceso denegado");

// 1. Obtener ID de usuario
if (!isset($_SESSION['id_usuario'])) {
    $stmtId = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE username = ?");
    $stmtId->execute([$_SESSION['Usuario']]);
    $userRow = $stmtId->fetch();
    $id_usuario = $userRow['id_usuario'] ?? null;
} else {
    $id_usuario = $_SESSION['id_usuario'];
}

if (!$id_usuario) exit("Usuario no encontrado");

// Limpiamos el nombre de espacios en blanco al inicio y final
$nuevo_nombre = isset($_POST['nuevo_usuario']) ? trim($_POST['nuevo_usuario']) : '';
$nombre_antiguo = $_SESSION['Usuario'];

try {
    // --- SECCIÓN: VALIDACIONES DE NOMBRE DE USUARIO ---
    if ($nuevo_nombre != $nombre_antiguo) {
        
        // A. Validación de longitud (Mínimo 3 caracteres)
        if (strlen($nuevo_nombre) < 3) {
            header("Location: ../PAGINAS/configuracionperfil.php?error=nombre_corto");
            exit();
        }

        // B. Validación de caracteres (Solo letras y números, sin símbolos ni espacios)
        if (!ctype_alnum($nuevo_nombre)) {
            header("Location: ../PAGINAS/configuracionperfil.php?error=nombre_especial");
            exit();
        }

        // C. Validación de disponibilidad (Si ya existe en la BD)
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE username = ?");
        $checkStmt->execute([$nuevo_nombre]);
        if ($checkStmt->fetchColumn() > 0) {
            header("Location: ../PAGINAS/configuracionperfil.php?error=nombre_duplicado");
            exit();
        }

        // Si pasa todas las validaciones, actualizamos en la BD y en la Sesión
        $stmt = $pdo->prepare("UPDATE usuarios SET username = ? WHERE id_usuario = ?");
        $stmt->execute([$nuevo_nombre, $id_usuario]);
        $_SESSION['Usuario'] = $nuevo_nombre;
    }
    
    // --- SECCIÓN: PROCESAR FOTO DE PERFIL ---
    if (isset($_FILES['nueva_foto']) && $_FILES['nueva_foto']['error'] == 0) {
        $directorio = "Recursos/fotos_perfil/";
        if (!is_dir($_SERVER['DOCUMENT_ROOT'] . "/" . $directorio)) { 
            mkdir($_SERVER['DOCUMENT_ROOT'] . "/" . $directorio, 0777, true); 
        }

        $extension = strtolower(pathinfo($_FILES['nueva_foto']['name'], PATHINFO_EXTENSION));
        $nombre_archivo = "user_" . $id_usuario . "_" . time() . "." . $extension;
        $ruta_final = "/" . $directorio . $nombre_archivo;

        if (move_uploaded_file($_FILES['nueva_foto']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $ruta_final)) {
            $stmt = $pdo->prepare("UPDATE usuarios SET avatar = ? WHERE id_usuario = ?");
            $stmt->execute([$ruta_final, $id_usuario]);
            $_SESSION['Foto'] = $ruta_final;
        }
    }

    // --- SECCIÓN: PROCESAR BANNER ---
    if (isset($_FILES['nuevo_banner']) && $_FILES['nuevo_banner']['error'] == 0) {
        $directorio_banner = "Recursos/Banners/";
        if (!is_dir($_SERVER['DOCUMENT_ROOT'] . "/" . $directorio_banner)) { 
            mkdir($_SERVER['DOCUMENT_ROOT'] . "/" . $directorio_banner, 0777, true); 
        }

        $extension_banner = strtolower(pathinfo($_FILES['nuevo_banner']['name'], PATHINFO_EXTENSION));
        $nombre_banner = "banner_" . $id_usuario . "_" . time() . "." . $extension_banner;
        $ruta_banner_final = "/" . $directorio_banner . $nombre_banner;

        if (move_uploaded_file($_FILES['nuevo_banner']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $ruta_banner_final)) {
            $stmt = $pdo->prepare("UPDATE usuarios SET banner = ? WHERE id_usuario = ?");
            $stmt->execute([$ruta_banner_final, $id_usuario]);
            $_SESSION['Banner'] = $ruta_banner_final;
        }
    }

    // REDIRECCIÓN FINAL DE ÉXITO
    header("Location: ../PAGINAS/listaperfil.php?status=updated");
    exit();

} catch (Exception $e) {
    die("Error crítico al actualizar: " . $e->getMessage());
}
?>