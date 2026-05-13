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

if ($action === 'rate') {
    $nota = intval($_POST['puntuacion']);
    $sql_rate = "INSERT INTO media_usuario (id_usuario, id_media, puntuacion) 
                 VALUES (?, ?, ?) 
                 ON DUPLICATE KEY UPDATE puntuacion = ?";
    $stmt_rate = $conexion->prepare($sql_rate);
    $stmt_rate->bind_param("iiii", $id_user, $id_media, $nota, $nota);
    $stmt_rate->execute();
    
    echo json_encode(['status' => 'success', 'result' => 'rated']);
    exit;
}

if ($action === 'update_status') {
    $nuevo_status = $_POST['nuevo_status'];
    $sql_status = "INSERT INTO media_usuario (id_usuario, id_media, status) 
                   VALUES (?, ?, ?) 
                   ON DUPLICATE KEY UPDATE status = ?";
    $stmt_status = $conexion->prepare($sql_status);
    $stmt_status->bind_param("iiss", $id_user, $id_media, $nuevo_status, $nuevo_status);
    $stmt_status->execute();
    
    echo json_encode(['status' => 'success', 'result' => 'status_updated']);
    exit;
}

// --- NUEVA Lógica de Personaje Favorito con su propia tabla ---
if ($action === 'fav_character') {
    $char_id   = $_POST['char_id'];
    $char_name = $_POST['char_name'];
    $char_img  = isset($_POST['char_img']) ? $_POST['char_img'] : ''; 

    if (!isset($conexion)) {
        echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
        exit;
    }

    // Buscamos si ESTE usuario ya tiene a ESTE personaje
    $check = $conexion->prepare("SELECT personaje_id FROM personajes_usuario WHERE id_usuario = ? AND personaje_id = ?");
    $check->bind_param("ii", $id_user, $char_id);
    $check->execute();
    $curr = $check->get_result()->fetch_assoc();

    if ($curr) {
        // Si ya existe, lo borramos de favoritos
        $stmt = $conexion->prepare("DELETE FROM personajes_usuario WHERE id_usuario = ? AND personaje_id = ?");
        $stmt->bind_param("ii", $id_user, $char_id);
        $res_type = 'removed';
    } else {
        // Si no existe, lo añadimos
        $stmt = $conexion->prepare("INSERT INTO personajes_usuario (id_usuario, id_media, personaje_id, personaje_nombre, personaje_imagen) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiss", $id_user, $id_media, $char_id, $char_name, $char_img);
        $res_type = 'added';
    }

    $stmt->execute();
    echo json_encode(['status' => 'success', 'result' => $res_type]);
    exit;
}
// --- NUEVA Lógica para Guardar Reseñas ---
if ($action === 'add_review') {
    $review_text = $_POST['review'] ?? '';

    if (strlen(trim($review_text)) < 10) {
        echo json_encode(['status' => 'error', 'message' => 'Reseña demasiado corta']);
        exit;
    }

    // Insertamos la reseña vinculándola al id_media que ya obtuviste al principio del archivo
    $stmt_rev = $conexion->prepare("INSERT INTO resenas (id_usuario, id_media, texto_resena, created_at) VALUES (?, ?, ?, NOW())");
    $stmt_rev->bind_param("iis", $id_user, $id_media, $review_text);
    
    if ($stmt_rev->execute()) {
        echo json_encode(['status' => 'success', 'result' => 'review_added']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conexion->error]);
    }
    exit;
}
// --- LOGICA DE LIMPIEZA ---
$clean = $conexion->prepare("DELETE FROM media_usuario WHERE id_usuario = ? AND id_media = ? AND (status IS NULL OR status = '') AND (es_favorito = 0 OR es_favorito IS NULL)");
$clean->bind_param("ii", $id_user, $id_media);
$clean->execute();

echo json_encode(['status' => 'success', 'result' => $final_state]);
?>