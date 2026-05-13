<?php
session_start();
require_once '../conexion.php'; 

if (!isset($_SESSION['Usuario'])) {
    exit("Acceso denegado. Debes iniciar sesión.");
}

// 1. Asegurarnos de tener el ID del usuario
if (!isset($_SESSION['id_usuario'])) {
    $stmtId = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE username = ?");
    $stmtId->execute([$_SESSION['Usuario']]);
    $userRow = $stmtId->fetch();
    $_SESSION['id_usuario'] = $userRow['id_usuario'] ?? null;
}

// ID de la lista a mostrar (la mía o la de otro usuario por perfil público)
$id_usuario_lista = isset($_GET['id']) ? (int)$_GET['id'] : $_SESSION['id_usuario'];

// 2. CONSULTA CORREGIDA
// Filtramos por 'movie' y 'tv' que son los tipos reales de TMDB en tu DB
$stmt = $pdo->prepare("
    SELECT 
        m.id_media,
        m.tmdb_id,
        m.mal_id,
        m.titulo, 
        m.portada, 
        m.type, 
        m.episodios_totales,
        mu.status, 
        mu.puntuacion, 
        mu.episodios_vistos 
    FROM media_usuario mu
    JOIN media m ON mu.id_media = m.id_media
    WHERE mu.id_usuario = ? 
    AND (m.type = 'movie' OR m.type = 'tv' OR m.type = 'pelicula') 
    ORDER BY FIELD(mu.status, 'watching', 'completed', 'on_hold', 'dropped', 'plan_to_watch'), m.titulo ASC
");

$stmt->execute([$id_usuario_lista]);
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Agrupar la lista por estados (Ajustado a los strings que usas en el resto de la web)
$listaAgrupada = [
    'watching'      => [],
    'completed'     => [],
    'on_hold'       => [],
    'dropped'       => [],
    'plan_to_watch' => []
];

foreach ($resultados as $item) {
    // Verificamos que el status exista en nuestro array para evitar errores de Undefined Index
    if (array_key_exists($item['status'], $listaAgrupada)) {
        $listaAgrupada[$item['status']][] = $item;
    } else {
        // Por si acaso algún estado viene vacío o diferente
        $listaAgrupada['plan_to_watch'][] = $item;
    }
}

// 4. Títulos para el HTML
$nombresEstados = [
    'watching'      => 'Watching',
    'completed'     => 'Completed',
    'on_hold'       => 'On Hold',
    'dropped'       => 'Dropped',
    'plan_to_watch' => 'Plan to Watch/Movie'
];
?>