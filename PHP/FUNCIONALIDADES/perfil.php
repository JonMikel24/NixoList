<?php
require_once '../conexion.php'; // Tu archivo de conexión PDO

// Verificar si el usuario está logueado
if (!isset($_SESSION['Usuario'])) {
    header("Location: ../Login/Index.php");
    exit();
}

$nombreUsuario = $_SESSION['Usuario'];

// 3. LOGICA DE BASE DE DATOS (Traída de tu perfil.php)
try {
    // Obtener el ID y datos del usuario actual
    $stmtUser = $pdo->prepare("SELECT id_usuario, created_at FROM usuarios WHERE username = ?");
    $stmtUser->execute([$nombreUsuario]);
    $userRow = $stmtUser->fetch();
    
    if (!$userRow) {
        die("Usuario no encontrado en la base de datos.");
    }

    $id_usuario = $userRow['id_usuario'];

    // Obtener Estadísticas
    $stmtStats = $pdo->prepare("SELECT * FROM estdisticas_usuario WHERE id_usuario = ?");
    $stmtStats->execute([$id_usuario]);
    $stats = $stmtStats->fetch() ?: ['puntuacion_media' => 0.00, 'animes_completados' => 0];

    // Obtener "Watching"
    $stmtWatching = $pdo->prepare("
        SELECT m.titulo, m.portada 
        FROM media_usuario mu 
        JOIN media m ON mu.id_media = m.id_media 
        WHERE mu.id_usuario = ? AND mu.status = 'watching'
    ");
    $stmtWatching->execute([$id_usuario]);
    $watchingList = $stmtWatching->fetchAll();

    // Obtener Favoritos
    $stmtFavs = $pdo->prepare("
        SELECT m.portada 
        FROM media_usuario mu 
        JOIN media m ON mu.id_media = m.id_media 
        WHERE mu.id_usuario = ? AND mu.es_favorito = 1
    ");
    $stmtFavs->execute([$id_usuario]);
    $favorites = $stmtFavs->fetchAll();

// Obtener Personajes Favoritos de la nueva tabla
    $stmtChars = $pdo->prepare("SELECT personaje_nombre, personaje_imagen FROM personajes_usuario WHERE id_usuario = ?");
    $stmtChars->execute([$id_usuario]);
    $favorite_characters = $stmtChars->fetchAll();
    
} catch (Exception $e) {
    die("Error al cargar datos del perfil: " . $e->getMessage());
}

// 4. FUNCION API (Si necesitas datos externos)
function callAPI($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response,true);
}
?>