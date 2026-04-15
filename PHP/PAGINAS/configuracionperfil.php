<?php
session_start();
// Opcional: Aquí deberías incluir tu conexión a la base de datos si necesitas obtener el banner desde allí
// require_once '../conexion.php'; 

if (!isset($_SESSION['Usuario'])) {
    header("Location: ../Login/Index.php");
    exit();
}

$nombreActual = $_SESSION['Usuario'];
$fotoActual = (!empty($_SESSION['Foto'])) ? $_SESSION['Foto'] : '/Recursos/fotousuario.png';

// Si guardas el banner en la sesión, ponlo aquí. Si no, pon una imagen por defecto para que no se vea roto.
$bannerActual = (!empty($_SESSION['Banner'])) ? $_SESSION['Banner'] : '/Recursos/fotousuario.png';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración de Perfil - Nixolist</title>
    <link rel="stylesheet" href="../../CSS/configuracion.css">
</head>
<body>

<div class="config-card">
    <div class="config-header">
        <h2>Ajustes de Perfil</h2>
    </div>
    
    <form action="../FUNCIONALIDADES/procesar_configuracion.php" method="POST" enctype="multipart/form-data">
        
        <div class="form-group">
            <label>Banner del Perfil</label>
            <div class="banner-preview">
                <img src="<?php echo htmlspecialchars($bannerActual); ?>" alt="Banner actual">
                <div class="upload-overlay">
                    <label for="banner-upload" class="custom-file-btn">Cambiar Banner</label>
                    <input type="file" id="banner-upload" name="nuevo_banner" accept="image/*">
                </div>
            </div>
        </div>

        <div class="form-group avatar-section">
            <label>Foto de Perfil</label>
            <div class="profile-preview">
                <img src="<?php echo htmlspecialchars($fotoActual); ?>" alt="Avatar actual">
                <div class="upload-overlay circle-overlay">
                    <label for="avatar-upload" class="custom-file-btn-small">✏️</label>
                    <input type="file" id="avatar-upload" name="nueva_foto" accept="image/*">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Nombre de Usuario</label>
            <input type="text" class="custom-input" name="nuevo_usuario" value="<?php echo htmlspecialchars($nombreActual); ?>" required>
        </div>

        <div class="form-actions">
            <button type="submit" class="save-btn">Guardar Cambios</button>
            <a href="listaperfil.php" class="back-link">← Cancelar y volver</a>
        </div>
    </form>
</div>

</body>
</html>