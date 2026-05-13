<?php
session_start();

if (!isset($_SESSION['Usuario'])) {
    header("Location: ../Login/Index.php");
    exit();
}

$nombreActual = $_SESSION['Usuario'];
$fotoActual = (!empty($_SESSION['Foto'])) ? $_SESSION['Foto'] : '/Recursos/fotousuario.png';
$bannerActual = (!empty($_SESSION['Banner'])) ? $_SESSION['Banner'] : '/Recursos/fotousuario.png';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración de Perfil - Nixolist</title>
    <link rel="stylesheet" href="../../CSS/configuracion.css">
    <style>
        .input-error { border: 2px solid #ff4d4d !important; }
        .error-msg { color: #ff4d4d; font-size: 0.9em; margin-bottom: 8px; font-weight: bold; display: block; }
    </style>
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
        
        <?php if (isset($_GET['error'])): ?>
            <p class="error-msg" style="color: #ff4d4d; font-size: 0.9em; margin-bottom: 5px; font-weight: bold;">
                <?php 
                    if ($_GET['error'] == 'nombre_duplicado') echo "⚠️ El nombre de usuario ya está en uso.";
                    if ($_GET['error'] == 'nombre_corto') echo "⚠️ El nombre debe tener al menos 3 caracteres.";
                    if ($_GET['error'] == 'nombre_especial') echo "⚠️ Solo se permiten letras y números.";
                ?>
            </p>
        <?php endif; ?>

        <input type="text" 
            class="custom-input <?php echo isset($_GET['error']) ? 'input-error' : ''; ?>" 
            name="nuevo_usuario" 
            value="<?php echo htmlspecialchars($nombreActual); ?>" 
            required>
        
        <small style="color: gray; display: block; margin-top: 5px;">Mínimo 3 caracteres, sin símbolos.</small>
    </div>

        <div class="form-actions">
            <button type="submit" class="save-btn">Guardar Cambios</button>
            <a href="../Login/logout.php" class="btn-logout">Cerrar Sesión</a>
            <a href="listaperfil.php" class="back-link">← Cancelar y volver</a>
        </div>
    </form>
</div>

<script>
    // Vista previa de Avatar
    const inputAvatar = document.getElementById('avatar-upload');
    const previewAvatar = document.getElementById('avatar-prev');
    if (inputAvatar && previewAvatar) {
        inputAvatar.addEventListener('change', e => {
            const file = e.target.files[0];
            if (file) previewAvatar.src = URL.createObjectURL(file);
        });
    }

    // Vista previa de Banner
    const inputBanner = document.getElementById('banner-upload');
    const previewBanner = document.getElementById('banner-prev');
    if (inputBanner && previewBanner) {
        inputBanner.addEventListener('change', e => {
            const file = e.target.files[0];
            if (file) previewBanner.src = URL.createObjectURL(file);
        });
    }
</script>
</body>
</html>