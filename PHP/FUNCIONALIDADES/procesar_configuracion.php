<?php
session_start();
require_once '../conexion.php'; // Asegúrate de que la ruta sea correcta

if (!isset($_SESSION['Usuario'])) exit("Acceso denegado");

// Intentar obtener el ID de la sesión, si no existe, lo buscamos (por seguridad)
if (!isset($_SESSION['id_usuario'])) {
    $stmtId = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE username = ?");
    $stmtId->execute([$_SESSION['Usuario']]);
    $userRow = $stmtId->fetch();
    $id_usuario = $userRow['id_usuario'];
} else {
    $id_usuario = $_SESSION['id_usuario'];
}

$nuevo_nombre = $_POST['nuevo_usuario'];
$nombre_antiguo = $_SESSION['Usuario'];

// --- FUNCIÓN PARA REDIMENSIONAR A 225x350 ---
function procesarYRedimensionar($ruta_temporal, $ruta_destino) {
    list($ancho_orig, $alto_orig, $tipo) = getimagesize($ruta_temporal);
    
    // Crear recurso de imagen según el tipo
    switch ($tipo) {
        case IMAGETYPE_JPEG: $img_orig = imagecreatefromjpeg($ruta_temporal); break;
        case IMAGETYPE_PNG:  $img_orig = imagecreatefrompng($ruta_temporal); break;
        case IMAGETYPE_GIF:  $img_orig = imagecreatefromgif($ruta_temporal); break;
        default: return false;
    }

    // Crear lienzo de 225x350
    $nuevo_ancho = 225;
    $nuevo_alto = 350;
    $lienzo = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);

    // Mantener transparencias si es PNG o GIF
    if ($tipo == IMAGETYPE_PNG || $tipo == IMAGETYPE_GIF) {
        imagealphablending($lienzo, false);
        imagesavealpha($lienzo, true);
    }

    // Redimensionar ajustando al tamaño exacto (estirando/encogiendo para llenar el lienzo)
    imagecopyresampled($lienzo, $img_orig, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho_orig, $alto_orig);

    // Guardar la imagen final sobreescribiendo el destino
    $exito = false;
    switch ($tipo) {
        case IMAGETYPE_JPEG: $exito = imagejpeg($lienzo, $ruta_destino, 90); break;
        case IMAGETYPE_PNG:  $exito = imagepng($lienzo, $ruta_destino); break;
        case IMAGETYPE_GIF:  $exito = imagegif($lienzo, $ruta_destino); break;
    }

    imagedestroy($img_orig);
    imagedestroy($lienzo);
    return $exito;
}

try {
    // 1. ACTUALIZAR NOMBRE DE USUARIO
    if ($nuevo_nombre != $nombre_antiguo) {
        $stmt = $pdo->prepare("UPDATE usuarios SET username = ? WHERE id_usuario = ?");
        $stmt->execute([$nuevo_nombre, $id_usuario]);
        $_SESSION['Usuario'] = $nuevo_nombre;
    }

    // 2. PROCESAR SUBIDA DE FOTO
    if (isset($_FILES['nueva_foto']) && $_FILES['nueva_foto']['error'] == 0) {
        $directorio = "Recursos/fotos_perfil/";
        
        // Creamos la carpeta si no existe
        if (!is_dir($directorio)) { mkdir($directorio, 0777, true); }

        $extension = pathinfo($_FILES['nueva_foto']['name'], PATHINFO_EXTENSION);
        $nombre_archivo = "user_" . $id_usuario . "_" . time() . "." . $extension;
        $ruta_final = "/" . $directorio . $nombre_archivo;

        if (move_uploaded_file($_FILES['nueva_foto']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $ruta_final)) {
            // Actualizar DB
            $stmt = $pdo->prepare("UPDATE usuarios SET avatar = ? WHERE id_usuario = ?");
            $stmt->execute([$ruta_final, $id_usuario]);
            
            // Actualizar Sesión
            $_SESSION['Foto'] = $ruta_final;
        }
    }


    header("Location: ../Paginas/listaperfil.php?status=updated");

} catch (Exception $e) {
    die("Error al actualizar: " . $e->getMessage());
}