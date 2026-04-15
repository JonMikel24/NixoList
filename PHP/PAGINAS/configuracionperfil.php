<?php
session_start();
if (!isset($_SESSION['Usuario'])) {
    header("Location: ../Login/Index.php");
    exit();
}

$nombreActual = $_SESSION['Usuario'];
$fotoActual = (!empty($_SESSION['Foto'])) ? $_SESSION['Foto'] : '/Recursos/fotousuario.png';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración de Perfil - Nixolist</title>
    <style>
        :root {
            --bg-dark: #050505;
            --bg-panel: #121212;
            --accent: #00ffff;
            --text-white: #eeeeee;
        }
        body { background: var(--bg-dark); color: var(--text-white); font-family: Verdana; margin: 0; padding: 40px; }
        .config-card { 
            max-width: 500px; 
            margin: 0 auto; 
            background: var(--bg-panel); 
            padding: 30px; 
            border: 1px solid #333; 
            border-radius: 8px;
        }
        .profile-preview { text-align: center; margin-bottom: 25px; }
        .profile-preview img { 
            width: 120px; height: 120px; 
            border-radius: 50%; border: 2px solid var(--accent); 
            object-fit: cover; margin-bottom: 10px;
        }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 14px; margin-bottom: 8px; color: #9b9b9b; }
        input[type="text"], input[type="file"] {
            width: 100%; padding: 10px; background: #1a1a1a; 
            border: 1px solid #333; color: white; border-radius: 4px; box-sizing: border-box;
        }
        .save-btn {
            width: 100%; padding: 12px; background: var(--accent); 
            color: black; border: none; border-radius: 4px; 
            font-weight: bold; cursor: pointer; transition: 0.3s;
        }
        .save-btn:hover { background: #00cccc; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #9b9b9b; text-decoration: none; font-size: 12px; }
    </style>
</head>
<body>

<div class="config-card">
    <h2 style="margin-top:0; border-bottom: 1px solid #333; padding-bottom: 10px;">Ajustes de Perfil</h2>
    
    <form action="../FUNCIONALIDADES/procesar_configuracion.php" method="POST" enctype="multipart/form-data">
        
        <div class="profile-preview">
            <img src="<?php echo htmlspecialchars($fotoActual); ?>" alt="Avatar actual">
            <p style="font-size: 12px;"><?php echo htmlspecialchars($nombreActual); ?></p>
        </div>

        <div class="form-group">
            <label>Nuevo Nombre de Usuario</label>
            <input type="text" name="nuevo_usuario" value="<?php echo htmlspecialchars($nombreActual); ?>" required>
        </div>

        <div class="form-group">
            <label>Cambiar Foto de Perfil</label>
            <input type="file" name="nueva_foto" accept="image/*">
        </div>

        <button type="submit" class="save-btn">Guardar Cambios</button>
        <a href="listaperfil.php" class="back-link">← Volver al Perfil</a>
    </form>
</div>

</body>
</html>