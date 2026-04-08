<?php
session_start();
require_once("../conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['status' => 'error', 'message' => 'Inicia sesión primero']);
    exit;
}

$id_user  = $_SESSION['id_usuario'];
$id_api   = $_POST['id_api'];
$type     = $_POST['type'];
$titulo   = $_POST['titulo'];
$portada  = $_POST['portada'];
$action   = $_POST['action'];

// 1. Asegurar que la obra existe en la tabla 'media'
$stmt = $conexion->prepare("SELECT id_media FROM media WHERE tmdb_id = ? OR mal_id = ?");
$stmt->bind_param("ss", $id_api, $id_api);
$stmt->execute();
$res = $stmt->get_result();
$media = $res->fetch_assoc();

if (!$media) {
    $ins = $conexion->prepare("INSERT INTO media (titulo, type, portada, tmdb_id, mal_id) VALUES (?, ?, ?, ?, ?)");
    $mal = ($type == 'anime') ? $id_api : null;
    $tmdb = ($type != 'anime') ? $id_api : null;
    $db_type = ($type == 'movie') ? 'pelicula' : $type;
    $ins->bind_param("sssss", $titulo, $db_type, $portada, $tmdb, $mal);
    $ins->execute();
    $id_media = $conexion->insert_id;
} else {
    $id_media = $media['id_media'];
}

// 2. Lógica de Toggle
$check_rel = $conexion->prepare("SELECT status, es_favorito FROM media_usuario WHERE id_usuario = ? AND id_media = ?");
$check_rel->bind_param("ii", $id_user, $id_media);
$check_rel->execute();
$relacion = $check_rel->get_result()->fetch_assoc();

$final_state = 'added';

if ($action === 'favorite') {
    if ($relacion) {
        $nuevo_fav = $relacion['es_favorito'] ? 0 : 1;
        $upd = $conexion->prepare("UPDATE media_usuario SET es_favorito = ? WHERE id_usuario = ? AND id_media = ?");
        $upd->bind_param("iii", $nuevo_fav, $id_user, $id_media);
        $upd->execute();
        $final_state = $nuevo_fav ? 'added' : 'removed';
    } else {
        $ins = $conexion->prepare("INSERT INTO media_usuario (id_usuario, id_media, es_favorito) VALUES (?, ?, 1)");
        $ins->bind_param("ii", $id_user, $id_media);
        $ins->execute();
    }
} else {
    if ($relacion && $relacion['status'] === 'planned') {
        $upd = $conexion->prepare("UPDATE media_usuario SET status = NULL WHERE id_usuario = ? AND id_media = ?");
        $upd->bind_param("ii", $id_user, $id_media);
        $upd->execute();
        $final_state = 'removed';
    } else {
        $sql = "INSERT INTO media_usuario (id_usuario, id_media, status) VALUES (?, ?, 'planned') 
                ON DUPLICATE KEY UPDATE status = 'planned'";
        $stmt_list = $conexion->prepare($sql);
        $stmt_list->bind_param("ii", $id_user, $id_media);
        $stmt_list->execute();
    }
}

// --- LOGICA DE LIMPIEZA ---

$clean = $conexion->prepare("DELETE FROM media_usuario WHERE id_usuario = ? AND id_media = ? AND (status IS NULL OR status = '') AND (es_favorito = 0 OR es_favorito IS NULL)");
$clean->bind_param("ii", $id_user, $id_media);
$clean->execute();

echo json_encode(['status' => 'success', 'result' => $final_state]);