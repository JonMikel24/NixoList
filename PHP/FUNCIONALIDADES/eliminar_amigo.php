<?php
session_start();
require_once '../conexion.php'; 

// Verificamos sesión y que venga el ID por POST
if (!isset($_SESSION['id_usuario']) || !isset($_POST['id_amigo_usuario'])) {
    header("Location: ../PAGINAS/listaperfil.php?error=data_missing");
    exit();
}

$mi_id = $_SESSION['id_usuario'];
$id_objetivo = (int)$_POST['id_amigo_usuario'];

try {
    // En tu tabla, la relación puede estar de dos formas:
    // 1. Tú agregaste (id_usuario = mi_id, id_amigo_usuario = id_objetivo)
    // 2. Él te agregó (id_usuario = id_objetivo, id_amigo_usuario = mi_id)
    
    $stmt = $pdo->prepare("
        DELETE FROM amigos 
        WHERE (id_usuario = :mi_id AND id_amigo_usuario = :id_obj) 
           OR (id_usuario = :id_obj AND id_amigo_usuario = :mi_id)
    ");
    
    $stmt->execute([
        'mi_id' => $mi_id,
        'id_obj' => $id_objetivo
    ]);

    // Éxito: volvemos al perfil
    header("Location: ../PAGINAS/listaperfil.php?status=deleted");
    exit();

} catch (PDOException $e) {
    // Si algo falla, puedes ver el error con: die($e->getMessage());
    header("Location: ../PAGINAS/listaperfil.php?error=sql");
    exit();
}