<?php
session_start();
// Ajusta esta ruta a tu archivo de conexión real si es necesario
require_once '../conexion.php'; 

if (!isset($_SESSION['Usuario'])) {
    exit("Acceso denegado. Debes iniciar sesión.");
}

// 1. Obtener ID del usuario
if (!isset($_SESSION['id_usuario'])) {
    $stmtId = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE username = ?");
    $stmtId->execute([$_SESSION['Usuario']]);
    $userRow = $stmtId->fetch();
    $id_usuario = $userRow['id_usuario'];
} else {
    $id_usuario = $_SESSION['id_usuario'];
}

// 2. CONSULTA MODIFICADA: Solo traemos lo que tenga type = 'ANIME'
$stmt = $pdo->prepare("
    SELECT 
        m.titulo, 
        m.portada, 
        m.type, 
        m.episodios_totales,
        mu.status, 
        mu.puntuacion, 
        mu.episodios_vistos 
    FROM media_usuario mu
    JOIN media m ON mu.id_media = m.id_media
    WHERE mu.id_usuario = ? AND (m.type = 'ANIME' OR m.type = 'anime')
    ORDER BY FIELD(mu.status, 'watching', 'completed', 'planned', 'paused', 'dropped'), m.titulo ASC
");
$stmt->execute([$id_usuario]);
$animes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Agrupar la lista por estados
$listaAgrupada = [
    'watching' => [],
    'completed' => [],
    'planned' => [],
    'paused' => [],
    'dropped' => []
];

foreach ($animes as $anime) {
    $listaAgrupada[$anime['status']][] = $anime;
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