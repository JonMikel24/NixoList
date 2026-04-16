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
<div class="profile-banner-container">
    <?php 
    // Igual que con el avatar: usamos la sesión. 
    // Añado $userRow como plan B por si al iniciar sesión aún no has cargado el banner en la variable de sesión.
    if (!empty($_SESSION['Banner'])) {
        $BannerFinal = $_SESSION['Banner'];
    } elseif (!empty($userRow['banner'])) {
        $BannerFinal = $userRow['banner'];
    } else {
        // Imagen por defecto si no hay banner
        $BannerFinal = '/Recursos/Banners/banner_default.jpg'; 
    }
    ?>
    <img src="<?php echo htmlspecialchars($BannerFinal); ?>" class="banner-image" alt="Banner">
</div>

<div class="profile-header-content">
    <div class="profile-avatar-wrapper">
        <?php 
        // Definimos la foto: si el usuario tiene una en su sesión la usamos, si no, cargamos la por defecto
        $FotoFinal = (!empty($_SESSION['Foto'])) ? $_SESSION['Foto'] : ''; 
        ?>
        <img src="<?php echo htmlspecialchars($FotoFinal); ?>" class="profile-avatar-main" alt="Avatar">
    </div>
    <div class="profile-user-info">
        <h2 class="profile-username"><?php echo htmlspecialchars($_SESSION['Usuario']); ?></h2>
        <div class="profile-meta">
            <span>Joined <?php echo date('M j, Y', strtotime($userRow['created_at'])); ?></span>
        </div>
    </div>
    <div class="profile-actions">
        <a href="configuracionperfil.php" class="btn-config">Configuración</a>
    </div>
</div>

<div class="profile-subnav">
    <a href="#" class="active">Overview</a>
    <a href="animelistusuario.php">Anime List</a>
    <a href="#">Manga List</a>
    <a href="#">Reviews</a>
</div>

<div class="profile-body-container">
    
    <div class="profile-left-col">
        
        <?php
        // Tu lógica PHP de estadísticas intacta
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
        $counts = array_map(fn($v) => $v ?? 0, $counts);
        ?>

        <div class="content-card">
            <h3 class="card-title">Anime Stats</h3>
            
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
                    <span class="stat-value"><?php echo number_format($stats['puntuacion_media'], 2); ?></span>
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
                    <?php foreach($watchingList as $anime): ?>
                    <div class="cover-card">
                        <img src="<?php echo htmlspecialchars($anime['portada']); ?>" title="<?php echo htmlspecialchars($anime['titulo']); ?>">
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
                    <span class="tab active">Anime</span>
                    <span class="tab">Characters</span>
                </div>
            </div>
            
            <div class="cover-row">
                <?php if(empty($favorites)): ?>
                    <p class="empty-text">Aún no tienes animes favoritos.</p>
                <?php else: ?>
                    <?php foreach($favorites as $fav): ?>
                    <div class="cover-card fav-card">
                        <img src="<?php echo htmlspecialchars($fav['portada']); ?>">
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>