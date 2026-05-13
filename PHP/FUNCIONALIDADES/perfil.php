<?php
require_once '../conexion.php'; // Tu archivo de conexión PDO

// Verificar si el usuario que está navegando está logueado
if (!isset($_SESSION['Usuario'])) {
    header("Location: ../Login/Index.php");
    exit();
}

// 3. LÓGICA DE BASE DE DATOS
try {
    // Usamos el ID del perfil que estamos visitando (viene del archivo principal).
    // Si por algún motivo no estuviera definido, usamos tu sesión por seguridad.
    $id_usuario = isset($id_perfil_visitado) ? $id_perfil_visitado : $_SESSION['id_usuario'];

    // --- Obtener Estadísticas ---
    $stmtStats = $pdo->prepare("SELECT * FROM estdisticas_usuario WHERE id_usuario = ?");
    $stmtStats->execute([$id_usuario]);
    $stats = $stmtStats->fetch() ?: ['puntuacion_media' => 0.00, 'animes_completados' => 0];

    // --- Obtener "Watching" --- (He dejado solo la versión correcta con mal_id y tmdb_id)
    $stmtWatching = $pdo->prepare("
        SELECT m.id_media, m.mal_id, m.tmdb_id, m.type, m.titulo, m.portada 
        FROM media_usuario mu 
        JOIN media m ON mu.id_media = m.id_media 
        WHERE mu.id_usuario = ? AND mu.status = 'watching'
    ");
    $stmtWatching->execute([$id_usuario]);
    $watchingList = $stmtWatching->fetchAll();

    // --- Obtener Favoritos ---
    $stmtFavs = $pdo->prepare("
        SELECT m.id_media, m.mal_id, m.tmdb_id, m.type, m.portada, m.titulo
        FROM media_usuario mu 
        JOIN media m ON mu.id_media = m.id_media 
        WHERE mu.id_usuario = ? AND mu.es_favorito = 1
    ");
    $stmtFavs->execute([$id_usuario]);
    $favorites = $stmtFavs->fetchAll();
     
        // --- OBTENER LAS RESEÑAS DEL USUARIO VISITADO ---
    $stmtRev = $pdo->prepare("
        SELECT r.texto_resena, r.created_at, m.titulo, m.portada, m.tmdb_id, m.mal_id, m.type
        FROM resenas r
        JOIN media m ON r.id_media = m.id_media
        WHERE r.id_usuario = ?
        ORDER BY r.created_at DESC
    ");
    $stmtRev->execute([$id_usuario]);
    $user_reviews = $stmtRev->fetchAll(PDO::FETCH_ASSOC);
    // 👇 AQUÍ ESTÁ LA SOLUCIÓN 👇
    // --- Obtener Personajes Favoritos ---
    $stmtChars = $pdo->prepare("
        SELECT personaje_nombre, personaje_imagen 
        FROM personajes_usuario 
        WHERE id_usuario = ?
    ");
    $stmtChars->execute([$id_usuario]);
    $favorite_characters = $stmtChars->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    die("Error al cargar datos del perfil: " . $e->getMessage());
}

// 4. FUNCIÓN API (Si necesitas datos externos)
if (!function_exists('callAPI')) {
    function callAPI($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response,true);
    }
}
?>