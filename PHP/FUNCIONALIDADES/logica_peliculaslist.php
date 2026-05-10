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

// 👇 EL CAMBIO CLAVE: Leer el ID de la URL si existe, sino usar el propio 👇
$id_usuario_lista = isset($_GET['id']) ? (int)$_GET['id'] : $_SESSION['id_usuario'];

// 2. CONSULTA MODIFICADA: Traemos lo que tenga type = 'pelicula' OR 'tv'
$stmt = $pdo->prepare("
    SELECT 
        m.tmdb_id,
        m.titulo, 
        m.portada, 
        m.type, 
        m.episodios_totales,
        mu.status, 
        mu.puntuacion, 
        mu.episodios_vistos 
    FROM media_usuario mu
    JOIN media m ON mu.id_media = m.id_media
    WHERE mu.id_usuario = ? AND (m.type = 'pelicula' OR m.type = 'tv')
    ORDER BY FIELD(mu.status, 'watching', 'completed', 'planned', 'paused', 'dropped'), m.titulo ASC
");


// Ejecutamos la consulta con el nuevo ID (el tuyo o el de tu amigo)
$stmt->execute([$id_usuario_lista]);
$animes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Agrupar la lista por estados
$listaAgrupada = [
    'watching' => [],
    'completed' => [],
    'planned' => [],
    'paused' => [],
    'dropped' => []
];

// (Nota: Aunque la variable se llame $animes, aquí estamos guardando películas y series de TV)
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