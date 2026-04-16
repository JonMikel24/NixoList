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
                <img src="<?php echo htmlspecialchars($bannerActual); ?>" alt="Banner actual" id="banner-prev">
                <div class="upload-overlay">
                    <label for="banner-upload" class="custom-file-btn">Cambiar Banner</label>
                    <input type="file" id="banner-upload" name="nuevo_banner" accept="image/*">
                </div>
            </div>
        </div>

        <div class="form-group avatar-section">
            <label>Foto de Perfil</label>
            <div class="profile-preview">
                <img src="<?php echo htmlspecialchars($fotoActual); ?>" alt="Avatar actual" id="avatar-prev">
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
            <a href="../Login/logout.php" class="btn-logout">Cerrar Sesión</a>
            <a href="listaperfil.php" class="back-link">← Cancelar y volver</a>
        </div>
    </form>
</div>
<script>
    // --- VISTA PREVIA DEL AVATAR ---
    const inputAvatar = document.getElementById('avatar-upload');
    const previewAvatar = document.getElementById('avatar-prev');

    if (inputAvatar && previewAvatar) {
        inputAvatar.addEventListener('change', function(event) {
            const file = event.target.files[0]; // Obtenemos el archivo seleccionado
            if (file) {
                // Creamos una URL temporal local para la imagen
                const objectURL = URL.createObjectURL(file);
                // Cambiamos el 'src' de la imagen por esta nueva URL temporal
                previewAvatar.src = objectURL;
            }
        });
    }

    // --- VISTA PREVIA DEL BANNER ---
    const inputBanner = document.getElementById('banner-upload');
    const previewBanner = document.getElementById('banner-prev');

    if (inputBanner && previewBanner) {
        inputBanner.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const objectURL = URL.createObjectURL(file);
                previewBanner.src = objectURL; 
                
                // NOTA: Si en tu diseño el banner es un 'div' con background-image en lugar de un <img>, 
                // borra la línea de arriba y usa esta:
                // previewBanner.style.backgroundImage = `url(${objectURL})`;
            }
        });
    }
</script>
</body>
</html>