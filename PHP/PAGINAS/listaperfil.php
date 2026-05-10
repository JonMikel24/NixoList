<?php
session_start();

// 1. IMPORTAR CONEXIÓN
require_once '../conexion.php'; 

// 2. ¿QUÉ PERFIL ESTAMOS MIRANDO?
// Si hay un "?id=X" en la URL, miramos ese. Si no hay nada, miramos el tuyo.
$id_perfil_visitado = isset($_GET['id']) ? (int)$_GET['id'] : $_SESSION['id_usuario'];
$es_mi_perfil = ($id_perfil_visitado === $_SESSION['id_usuario']);

// 3. OBTENER LOS DATOS BÁSICOS DEL PERFIL VISITADO (No de tu sesión)
$stmtUsuario = $pdo->prepare("SELECT username, avatar, banner, created_at FROM usuarios WHERE id_usuario = ?");
$stmtUsuario->execute([$id_perfil_visitado]);
$datosVisitado = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

// Si el usuario no existe (alguien puso una ID falsa), detenemos la página
if (!$datosVisitado) {
    die("Este usuario no existe o ha sido eliminado.");
}

// Preparamos las imágenes del usuario visitado (usando la foto y banner de la BD, o las de por defecto)
$AvatarVisitado = (!empty($datosVisitado['avatar'])) ? $datosVisitado['avatar'] : '/Recursos/fotousuario.png';
$BannerVisitado = (!empty($datosVisitado['banner'])) ? $datosVisitado['banner'] : '../Recursos/Banners/banner_default.jpg';

// 4. COMPROBAR AMISTAD (Solo si no es mi perfil)
$son_amigos = false;
if (!$es_mi_perfil) {
    // IMPORTANTE: Asegúrate de que en tu BD tienes una tabla llamada 'amigos' 
    // con las columnas 'id_usuario' (tú) e 'id_amigo' (él)
    $stmtAmigo = $pdo->prepare("SELECT 1 FROM amigos WHERE id_usuario = ? AND id_amigo = ?");
    $stmtAmigo->execute([$_SESSION['id_usuario'], $id_perfil_visitado]);
    if ($stmtAmigo->fetchColumn()) {
        $son_amigos = true;
    }
}

// 5. IMPORTAR LÓGICA DE LISTAS
// ⚠️ ATENCIÓN AQUÍ: Tienes que editar este archivo 'perfil.php' para que use 
// la variable $id_perfil_visitado en lugar de usar $_SESSION['id_usuario']
require_once '../FUNCIONALIDADES/perfil.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="../../CSS/listaperfil.css">
    <link rel="stylesheet" href="../../CSS/styles.css">

    <meta charset="UTF-8">
    <title>Perfil de <?php echo htmlspecialchars($datosVisitado['username']); ?> - Nixolist</title>
</head>
<body>

<header class="header-main">
    <div class="header-top">
        <div class="logo-container">
            <h1 class="logo-texto">NixoList</h1>
        </div>

        <div class="PerfilContenedor"> <?php
            // El header NO se toca, porque arriba a la derecha siempre deben salir TUS datos, no los de tu amigo
            if (isset($_SESSION['Usuario'])) {
                $Foto = (!empty($_SESSION['Foto'])) ? $_SESSION['Foto'] : '/Recursos/fotousuario.png';
                echo '
                <a href="listaperfil.php" style="text-decoration: none; color: inherit;">
                    <div class="perfil-horiz">
                        <div class="perfil-info">
                            <p class="perfil-nombre nombre-mio">' . htmlspecialchars($_SESSION['Usuario']) . ' <span class="flecha">▼</span></p>
                        </div>
                        <img src="' . htmlspecialchars($Foto) . '" class="profile-pic foto-mia" id="perfilImagen">
                    </div>
                </a>
                ';
            } else {
                echo '
                <div class="auth-buttons">
                    <a href="../Login/Index.php"><button class="login-btn">Iniciar Sesión</button></a>
                    <a href="../Login/registrarse.php"><button class="register-btn">Registrarse</button></a>
                </div>
                ';
            }
            ?>
        </div>
    </div>
</header>
    <?php
    $pagina_actual = basename($_SERVER['PHP_SELF']);
    ?>

<nav class="navbar">
        <div class="nav-links">
            <a href="index.php" class="<?php echo ($pagina_actual == 'index.php') ? 'active' : ''; ?>">Inicio</a>
            <a href="anime.php" class="<?php echo ($pagina_actual == 'anime.php') ? 'active' : ''; ?>">Anime</a>
            <a href="peliculas.php" class="<?php echo ($pagina_actual == 'peliculas.php') ? 'active' : ''; ?>">Películas</a>
            <a href="series.php" class="<?php echo ($pagina_actual == 'series.php') ? 'active' : ''; ?>">Series</a>
        </div>

    <div class="search-container">
        <select class="search-select">
            <option value="all">All</option>
            <option value="anime">Anime</option>
            <option value="manga">Manga</option>
        </select>
        <input type="text" placeholder="Search Anime, Manga, and more..." class="search-input">
        <button type="submit" class="search-button">
            <i>🔍</i> </button>
    </div>
</nav>

<div class="profile-banner-container">
    <img src="<?php echo htmlspecialchars($BannerVisitado); ?>" class="banner-image" alt="Banner">
</div>

<div class="profile-header-content">
    <div class="profile-avatar-wrapper">
        <img src="<?php echo htmlspecialchars($AvatarVisitado); ?>" class="profile-avatar-main" alt="Avatar">
    </div>
    <div class="profile-user-info">
        <h2 class="profile-username"><?php echo htmlspecialchars($datosVisitado['username']); ?></h2>
        <div class="profile-meta">
            <span>Joined <?php echo date('M j, Y', strtotime($datosVisitado['created_at'])); ?></span>
        </div>
    </div>
    
    <div class="profile-actions">
        <?php if ($es_mi_perfil): ?>
            <a href="configuracionperfil.php" class="btn-config">Configuración</a>
        <?php else: ?>
            <?php if ($son_amigos): ?>
                <form action="../FUNCIONALIDADES/eliminar_amigo.php" method="POST" style="margin: 0;">
                    <input type="hidden" name="amigo_id" value="<?php echo $id_perfil_visitado; ?>">
                    <button type="submit" class="btn-config" style="background: #dc2626; border-color: #dc2626; color: white;">Eliminar Amigo</button>
                </form>
            <?php else: ?>
                <form action="../FUNCIONALIDADES/anadir_amigo.php" method="POST" style="margin: 0;">
                    <input type="hidden" name="amigo_id" value="<?php echo $id_perfil_visitado; ?>">
                    <button type="submit" class="btn-config" style="background: #4ade80; border-color: #4ade80; color: #111; font-weight: bold;">+ Añadir Amigo</button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    
</div>

<div class="profile-subnav">
    <a href="listaperfil.php?id=<?php echo $id_perfil_visitado; ?>" class="active">Overview</a>
    <a href="animelistusuario.php?id=<?php echo $id_perfil_visitado; ?>">Anime List</a>
    <a href="#">Manga List</a>
    <a href="peliculatvlistusuario.php?id=<?php echo $id_perfil_visitado; ?>">TV List</a>
    <a href="amigos.php?id=<?php echo $id_perfil_visitado; ?>">Friends</a>
    <a href="reseñasperfil.php">Reviews</a>
</div>

<div class="profile-body-container">
    
    <div class="profile-left-col">
        
        <?php
        $tipos_permitidos = ['anime', 'pelicula', 'tv', 'libro'];
        $tipo_seleccionado = (isset($_GET['tipo']) && in_array($_GET['tipo'], $tipos_permitidos)) ? $_GET['tipo'] : 'anime';

        $stmt = $pdo->prepare("
            SELECT 
                SUM(CASE WHEN mu.status = 'watching' THEN 1 ELSE 0 END) as watching,
                SUM(CASE WHEN mu.status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN mu.status = 'planned' THEN 1 ELSE 0 END) as planned,
                SUM(CASE WHEN mu.status = 'dropped' THEN 1 ELSE 0 END) as dropped,
                COUNT(mu.id_usuario) as total_entries,
                AVG(NULLIF(mu.puntuacion, 0)) as mean_score
            FROM media_usuario mu
            JOIN media m ON mu.id_media = m.id_media
            WHERE mu.id_usuario = ? AND m.type = ?
        ");
        
        // ¡Cambiado para que busque las stats del perfil visitado!
        $stmt->execute([$id_perfil_visitado, $tipo_seleccionado]);
        $counts = $stmt->fetch(PDO::FETCH_ASSOC);
        $counts = array_map(fn($v) => $v ?? 0, $counts); 
        
        $titulo_stats = ucfirst($tipo_seleccionado) . " Stats";
        ?>
        
        <div class="content-card">
            <div class="stats-header">
                <h3 class="card-title" style="margin: 0;"><?php echo $titulo_stats; ?></h3>
                
                <form method="GET" action="" style="margin: 0;">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id_perfil_visitado); ?>">
                    <select name="tipo" class="stats-select" onchange="this.form.submit()">
                        <option value="anime" <?php echo $tipo_seleccionado == 'anime' ? 'selected' : ''; ?>>Anime</option>
                        <option value="tv" <?php echo $tipo_seleccionado == 'tv' ? 'selected' : ''; ?>>Series TV</option>
                        <option value="pelicula" <?php echo $tipo_seleccionado == 'pelicula' ? 'selected' : ''; ?>>Películas</option>
                        <option value="libro" <?php echo $tipo_seleccionado == 'libro' ? 'selected' : ''; ?>>Libros</option>
                    </select>
                </form>
            </div>
            <div class="stats-grid">
                <div class="stat-box">
                    <span class="stat-value" style="color: #ffd100;"><?php echo $counts['total_entries']; ?></span>
                    <span class="stat-label">Total Anime</span>
                </div>
                <div class="stat-box">
                    <span class="stat-value" style="color: #4ade80;"><?php echo $counts['completed']; ?></span>
                    <span class="stat-label">Completed</span>
                </div>
                <div class="stat-box">
                    <span class="stat-value"><?php echo number_format($counts['mean_score'], 2); ?></span>
                    <span class="stat-label">Mean Score</span>
                </div>
            </div>

            <div class="stats-bar-wrapper">
                <div class="stats-bar-fill completed" style="width: <?php echo ($counts['total_entries'] > 0) ? ($counts['completed'] / $counts['total_entries'] * 100) : 0; ?>%"></div>
                <div class="stats-bar-fill watching" style="width: <?php echo ($counts['total_entries'] > 0) ? ($counts['watching'] / $counts['total_entries'] * 100) : 0; ?>%"></div>
                <div class="stats-bar-fill planned" style="width: <?php echo ($counts['total_entries'] > 0) ? ($counts['planned'] / $counts['total_entries'] * 100) : 0; ?>%"></div>
            </div>

            <div class="stats-legend">
                <span><div class="legend-dot completed"></div> <?php echo $counts['completed']; ?> Completed</span>
                <span><div class="legend-dot watching"></div> <?php echo $counts['watching']; ?> Watching</span>
                <span><div class="legend-dot planned"></div> <?php echo $counts['planned']; ?> Planned</span>
                <span><div class="legend-dot dropped"></div> <?php echo $counts['dropped']; ?> Dropped</span>
            </div>
        </div>
    </div>

    <div class="profile-right-col">
        
<div class="content-section">
            <h3 class="section-title">Currently Watching</h3>
            <div class="cover-row">
                <?php if(empty($watchingList)): ?>
                    <p class="empty-text">No estás viendo nada actualmente.</p>
                <?php else: ?>
                    <?php foreach($watchingList as $anime): 
                        // Decidimos qué ID usar según el tipo
                        $id_para_link = (strtolower($anime['type']) === 'anime') ? $anime['mal_id'] : $anime['tmdb_id'];
                    ?>
                    <div class="cover-card fav-card">
                        <a href="media.php?id=<?php echo urlencode($id_para_link); ?>&type=<?php echo urlencode($anime['type']); ?>">
                            <img src="<?php echo htmlspecialchars($anime['portada']); ?>" title="<?php echo htmlspecialchars($anime['titulo']); ?>">
                        </a>
                        <div class="cover-title"><?php echo htmlspecialchars($anime['titulo']); ?></div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    <div class="content-section">
        <div class="section-header-flex">
            <h3 class="section-title">Favorites</h3>
            <div class="favorite-tabs">
                <span class="tab active" onclick="cambiarPestaña('anime', this)">Anime</span>
                <span class="tab" onclick="cambiarPestaña('personajes', this)">Characters</span>
            </div>
        </div>
        
        <div id="contenedor-anime" class="cover-row">
            <?php if(empty($favorites)): ?>
                <p class="empty-text">Aún no tienes animes favoritos.</p>
            <?php else: ?>
                <?php foreach($favorites as $fav): 
                    // Decidimos qué ID usar según el tipo
                    $id_para_link = (strtolower($fav['type']) === 'anime') ? $fav['mal_id'] : $fav['tmdb_id'];
                ?>
                    <div class="cover-card fav-card">
                        <a href="media.php?id=<?php echo urlencode($id_para_link); ?>&type=<?php echo urlencode($fav['type']); ?>">
                            <img 
                                src="<?php echo htmlspecialchars($fav['portada'] ?? ''); ?>" 
                                alt="Anime Cover"
                                title="<?php echo htmlspecialchars($fav['titulo'] ?? ''); ?>"
                            >
                        </a>
                        <div class="cover-title"><?php echo htmlspecialchars($fav['titulo'] ?? 'Sin título'); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div id="contenedor-personajes" class="cover-row" style="display: none;">
            <?php if(empty($favorite_characters)): ?>
                <p class="empty-text">Aún no tienes personajes favoritos.</p>
            <?php else: ?>
                <?php foreach($favorite_characters as $char): ?>
                <div class="cover-card fav-card">
                    <img 
                        src="<?php echo htmlspecialchars($char['personaje_imagen'] ?? ''); ?>" 
                        alt="Character Cover"
                    >
                    <div class="cover-title"><?php echo htmlspecialchars($char['personaje_nombre'] ?? ''); ?></div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    <script>
    function cambiarPestaña(tipo, elementoPestaña) {
        const pestañas = document.querySelectorAll('.favorite-tabs .tab');
        pestañas.forEach(pestaña => pestaña.classList.remove('active'));
        
        elementoPestaña.classList.add('active');
        
        document.getElementById('contenedor-anime').style.display = 'none';
        document.getElementById('contenedor-personajes').style.display = 'none';
        
        document.getElementById('contenedor-' + tipo).style.display = 'flex'; 
    }
    </script>

    </div>
</div>
</body>
</html>