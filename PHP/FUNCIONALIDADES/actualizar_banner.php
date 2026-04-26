<?php
session_start();
require_once '../conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../Login/Index.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

if (isset($_FILES['nuevo_banner']) && $_FILES['nuevo_banner']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['nuevo_banner']['tmp_name'];
    $fileName = $_FILES['nuevo_banner']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg', 'webp');

    if (in_array($fileExtension, $allowedfileExtensions)) {
        $newFileName = 'banner_' . $id_usuario . '_' . time() . '.' . $fileExtension;
        $uploadFileDir = '../Recursos/Banners/'; 
        
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0755, true);
        }
        
        $dest_path = $uploadFileDir . $newFileName;

        if(move_uploaded_file($fileTmpPath, $dest_path)) {
            
            // CORRECCIÓN: Guardamos la ruta con ../ para que HTML la encuentre desde PAGINAS
            $ruta_bd = '../Recursos/Banners/' . $newFileName;

            $stmt = $pdo->prepare("UPDATE usuarios SET banner = ? WHERE id_usuario = ?");
            if($stmt->execute([$ruta_bd, $id_usuario])) {
                
                // Actualizamos la sesión con la ruta correcta
                $_SESSION['Banner'] = $ruta_bd; 
                
                header("Location: ../PAGINAS/listaperfil.php?status=success");
                exit();
            }
        }
    }
}
header("Location: ../PAGINAS/listaperfil.php?status=error");
exit();
?>