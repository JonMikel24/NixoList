<?php
session_start();
// Ajusta esta ruta a tu archivo de conexión real si es necesario
require_once '../conexion.php'; 

if (!isset($_SESSION['Usuario'])) {
    exit("Acceso denegado. Debes iniciar sesión.");
}

// 1. Asegurarnos de tener el ID de la sesión guardado por si acaso
if (!isset($_SESSION['id_usuario'])) {
    $stmtId = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE username = ?");
    $stmtId->execute([$_SESSION['Usuario']]);
    $userRow = $stmtId->fetch();
    $_SESSION['id_usuario'] = $userRow['id_usuario'];
}

// Leemos la ID de la URL (si es que estás visitando a un amigo). Si no, usamos la tuya.
$id_usuario_lista = isset($_GET['id']) ? (int)$_GET['id'] : $_SESSION['id_usuario'];

// 2. CONSULTA MODIFICADA: Ahora traemos también mal_id y tmdb_id
$stmt = $pdo->prepare("
    SELECT 
        m.mal_id,            /* <-- AÑADIDO */
        m.tmdb_id,           /* <-- AÑADIDO */
        m.titulo, 
        m.portada, 
        m.type, 
        m.episodios_totales,
        mu.status, 
        mu.puntuacion, 
        mu.episodios_vistos 
    FROM media_usuario mu
    JOIN media m ON mu.id_media = m.id_media
    WHERE mu.id_usuario = ? AND (m.type = 'MANGA' OR m.type = 'manga')
    ORDER BY FIELD(mu.status, 'watching', 'completed', 'planned', 'paused', 'dropped'), m.titulo ASC
");

// Pasamos el nuevo ID a la consulta
$stmt->execute([$id_usuario_lista]);
$mangas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Agrupar la lista por estados
$listaAgrupada = [
    'watching' => [],
    'completed' => [],
    'planned' => [],
    'paused' => [],
    'dropped' => []
];

foreach ($mangas as $manga) {
    $listaAgrupada[$manga['status']][] = $manga;
}

// 4. Títulos bonitos para el HTML
$nombresEstados = [
    'watching' => 'Currently Watching',
    'completed' => 'Completed',
    'planned' => 'Plan to Watch',
    'paused' => 'On Hold',
    'dropped' => 'Dropped'
];
?>