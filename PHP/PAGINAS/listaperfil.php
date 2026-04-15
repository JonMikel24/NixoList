<?php
session_start();

// 1. IMPORTAR CONEXIÓN (Ajusta la ruta según tu carpeta)
// Si listaperfil.php está en la misma carpeta que perfil.php, deja 'config.php'
// Si está en otra, quizás sea '../config.php'
require_once '../conexion.php'; 
require_once '../FUNCIONALIDADES/perfil.php';
// 2. VERIFICACIÓN DE SESIÓN


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="../../CSS/listaperfil.css">
    <link rel="stylesheet" href="../../CSS/styles.css">


    <meta charset="UTF-8">
    <title>Perfil de <?php echo htmlspecialchars($nombreUsuario); ?> - Nixolist</title>
</head>
<body>

<header class="header-main">
    <div class="header-top">
        <div class="logo-container">
            <h1 class="logo-texto">NixoList</h1>
        </div>

        <div class="PerfilContenedor"> <?php
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
    // Obtenemos el nombre del archivo actual (ej: anime.php)
    $pagina_actual = basename($_SERVER['PHP_SELF']);
    ?>

<nav class="navbar">
        <div class="nav-links">
            <a href="index.php" class="<?php echo ($pagina_actual == 'index.php') ? 'active' : ''; ?>">Incio</a>
            <a href="anime.php" class="<?php echo ($pagina_actual == 'anime.php') ? 'active' : ''; ?>">Anime</a>
            <a href="peliculas.php" class="<?php echo ($pagina_actual == 'peliculas.php') ? 'active' : ''; ?>">Películas</a>
            <a href="series.php" class="<?php echo ($pagina_actual == 'series.php') ? 'active' : ''; ?>">Series</a>
        </div>

        <div class="search-container">
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

<div class="container">
    <aside class="sidebar">
        <?php $FotoFinal = (!empty($_SESSION['Foto'])) ? $_SESSION['Foto'] : '/Recursos/fotousuario.png'; ?>
        <img src="<?php echo htmlspecialchars($FotoFinal); ?>" class="profile-pic-main">

        <div class="user-status-box">
            <div style="display:flex; justify-content:space-between; margin-bottom: 5px;">
                <span>Joined</span>
                <span style="color:var(--text-dim)"><?php echo date('M j, Y', strtotime($userRow['created_at'])); ?></span>
            </div>
            <div style="display:flex; justify-content:space-between;">
                <span>Online</span>
                <span style="color:var(--mal-cyan)">Now</span>
            </div>
        </div>

        <a href="#" style="display:block; background:var(--mal-blue); color:white; text-align:center; padding:8px; margin-top:10px; text-decoration:none; font-size:12px; font-weight:bold;">Anime List</a>
        <a href="#" style="display:block; background:#333; color:white; text-align:center; padding:8px; margin-top:5px; text-decoration:none; font-size:12px;">Manga List</a>
    </aside>

    <main class="main-content">
        <h2 style="margin-top:0;"><?php echo htmlspecialchars($_SESSION['Usuario']); ?>'s Profile</h2>
        <a href="configuracionperfil.php">Configuracion</a>

        <h3 class="section-title">Watching this season</h3>
        <div class="cover-grid">
            <?php if(empty($watchingList)): ?>
                <p style="font-size:12px; color:var(--text-dim)">No estás viendo nada actualmente.</p>
            <?php else: ?>
                <?php foreach($watchingList as $anime): ?>
                <div class="cover-item">
                    <img src="<?php echo htmlspecialchars($anime['portada']); ?>" title="<?php echo htmlspecialchars($anime['titulo']); ?>">
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>  

        <?php
        // Primero, obtenemos el conteo real de cada estado para los gráficos y la lista
        $stmt = $pdo->prepare("
            SELECT 
                SUM(CASE WHEN status = 'watching' THEN 1 ELSE 0 END) as watching,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'planned' THEN 1 ELSE 0 END) as planned,
                SUM(CASE WHEN status = 'dropped' THEN 1 ELSE 0 END) as dropped,
                COUNT(*) as total_entries
            FROM media_usuario 
            WHERE id_usuario = ?
        ");
        $stmt->execute([$id_usuario]);
        $counts = $stmt->fetch(PDO::FETCH_ASSOC);

        // Evitar errores si no hay datos
        $counts = array_map(fn($v) => $v ?? 0, $counts);
        ?>

        <h3 class="section-title">Anime Stats</h3>
        <div class="stats-container-horizontal">
            <div class="stats-graph">
                <div class="graph-watching" style="width: <?php echo ($counts['total_entries'] > 0) ? ($counts['watching'] / $counts['total_entries'] * 100) : 0; ?>%"></div>
                <div class="graph-completed" style="width: <?php echo ($counts['total_entries'] > 0) ? ($counts['completed'] / $counts['total_entries'] * 100) : 0; ?>%"></div>
                <div class="graph-dropped" style="width: <?php echo ($counts['total_entries'] > 0) ? ($counts['dropped'] / $counts['total_entries'] * 100) : 0; ?>%"></div>
                <div class="graph-planned" style="width: <?php echo ($counts['total_entries'] > 0) ? ($counts['planned'] / $counts['total_entries'] * 100) : 0; ?>%"></div>
            </div>

            <div class="stats-columns">
                <div class="stat-col">
                    <p><span class="dot watching"></span> Watching: <strong><?php echo $counts['watching']; ?></strong></p>
                    <p><span class="dot completed"></span> Completed: <strong><?php echo $counts['completed']; ?></strong></p>
                    <p><span class="dot planned"></span> Plan to Watch: <strong><?php echo $counts['planned']; ?></strong></p>
                </div>
                <div class="stat-col">
                    <p>Total Entries: <strong><?php echo $counts['total_entries']; ?></strong></p>
                    <p>Mean Score: <strong><?php echo number_format($stats['puntuacion_media'], 2); ?></strong></p>
                </div>
            </div>
        </div>

        <h3 class="section-title">Favorites</h3>
        <div class="cover-grid" style="grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));">
            <?php if(empty($favorites)): ?>
                <p style="font-size:12px; color:var(--text-dim)">Aún no tienes favoritos.</p>
            <?php else: ?>
                <?php foreach($favorites as $fav): ?>
                <div class="cover-item">
                    <img src="<?php echo htmlspecialchars($fav['portada']); ?>" style="height:120px;">
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</div>

</body>
</html>