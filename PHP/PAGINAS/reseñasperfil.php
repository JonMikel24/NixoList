<?php
session_start();

// 1. IMPORTAR CONEXIÓN
require_once '../conexion.php'; 

// 2. ¿QUÉ PERFIL ESTAMOS MIRANDO?
$id_perfil_visitado = isset($_GET['id']) ? (int)$_GET['id'] : $_SESSION['id_usuario'];
$es_mi_perfil = ($id_perfil_visitado === $_SESSION['id_usuario']);

// 3. OBTENER DATOS DEL USUARIO
$stmtUsuario = $pdo->prepare("SELECT username, avatar, banner, created_at FROM usuarios WHERE id_usuario = ?");
$stmtUsuario->execute([$id_perfil_visitado]);
$datosVisitado = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

if (!$datosVisitado) {
    die("Este usuario no existe.");
}

$AvatarVisitado = (!empty($datosVisitado['avatar'])) ? $datosVisitado['avatar'] : '/Recursos/fotousuario.png';
$BannerVisitado = (!empty($datosVisitado['banner'])) ? $datosVisitado['banner'] : '../Recursos/Banners/banner_default.jpg';

// 4. OBTENER LAS RESEÑAS (La lógica que pedías)
$stmtRev = $pdo->prepare("
    SELECT r.texto_resena, r.created_at, m.titulo, m.portada, m.tmdb_id, m.mal_id, m.type
    FROM resenas r
    JOIN media m ON r.id_media = m.id_media
    WHERE r.id_usuario = ?
    ORDER BY r.created_at DESC
");
$stmtRev->execute([$id_perfil_visitado]);
$user_reviews = $stmtRev->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="../../CSS/listaperfil.css">
    <link rel="stylesheet" href="../../CSS/styles.css">
    <meta charset="UTF-8">
    <title>Reseñas de <?php echo htmlspecialchars($datosVisitado['username']); ?></title>
</head>
<body>
<header class="header-main">
    <div class="header-top">
        <div class="logo-container">
            <a href="index.php" class="enlace-logo">
                <h1 class="logo-texto">NixoList</h1>
            </a>
        </div>

        <div class="PerfilContenedor"> <?php
            if (isset($_SESSION['Usuario'])) {
                $Foto = (!empty($_SESSION['Foto'])) ? $_SESSION['Foto'] : '../../Recursos/fotos_perfil/fotousuario.png';
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

        <div class="menu-desplegable">
            <a href="anime.php" class="seccion-principal <?php echo ($pagina_actual == 'anime.php') ? 'active' : ''; ?>">Anime</a>
            <div class="sub-menu">
                <a href="anime.php">Inicio Anime</a>
                <a href="anime.php#recomendados">Recomendados</a>
                <a href="anime.php#populares">Más Populares</a>
                <a href="anime.php#top">Top Anime</a>
            </div>
        </div>

        <div class="menu-desplegable">
            <a href="manga.php" class="seccion-principal <?php echo ($pagina_actual == 'manga.php') ? 'active' : ''; ?>">Manga</a>
            <div class="sub-menu">
                <a href="manga.php">Inicio Manga</a>
                <a href="manga.php#recomendados">Recomendados</a>
                <a href="manga.php#populares">Más Populares</a>
                <a href="manga.php#top">Top Manga</a>
            </div>
        </div>

        <div class="menu-desplegable">
            <a href="peliculas.php" class="seccion-principal <?php echo ($pagina_actual == 'peliculas.php') ? 'active' : ''; ?>">Películas</a>
            <div class="sub-menu">
                <a href="peliculas.php">Inicio Películas</a>
                <a href="peliculas.php#recomendadas">Recomendadas</a>
                <a href="peliculas.php#populares">Más Populares</a>
                <a href="peliculas.php#top">Top Rated</a>
            </div>
        </div>

        <div class="menu-desplegable">
            <a href="series.php" class="seccion-principal <?php echo ($pagina_actual == 'series.php') ? 'active' : ''; ?>">Series</a>
            <div class="sub-menu">
                <a href="series.php">Inicio Series</a>
                <a href="series.php#trending">Trending</a>
                <a href="series.php#populares">Más Populares</a>
                <a href="series.php#top">Top Rated</a>
            </div>
        </div>

        <div class="menu-desplegable">
            <a href="juegos.php" class="seccion-principal <?php echo (in_array($pagina_actual, ['juegos.php', 'juegoOpeningsAnime.php', 'juegoPersonajesAnime.php', 'juegoWordleAnime.php'])) ? 'active' : ''; ?>">Juegos</a>
            <div class="sub-menu">
                <a href="juegos.php">Inicio Juegos</a>
                <a href="juegoOpeningsAnime.php">Adivina el Opening</a>
                <a href="juegoPersonajesAnime.php">Adivina el Personaje</a>
                <a href="juegoWordleAnime.php">Wordle Anime</a>
            </div>
        </div>
    </div>

    <div class="search-wrapper" style="position: relative;"> 
        <div class="search-container">
            <select class="search-select" id="search-type">
                <option value="all">All</option>
                <option value="anime">Anime</option>
                <option value="manga">Manga</option>
                <option value="movie">Películas</option>
                <option value="tv">Series</option>
            </select>
            <input type="text" id="search-input" placeholder="Search..." class="search-input" autocomplete="off">
            <button type="submit" class="search-button"><i>🔍</i></button>
        </div>
        <div id="search-results" class="search-results-dropdown"></div>
    </div>
</nav>

<div class="profile-banner-container">
    <img src="<?php echo htmlspecialchars($BannerVisitado); ?>" class="banner-image">
</div>

<div class="profile-header-content">
    <div class="profile-avatar-wrapper">
        <img src="<?php echo htmlspecialchars($AvatarVisitado); ?>" class="profile-avatar-main">
    </div>
    <div class="profile-user-info">
        <h2 class="profile-username"><?php echo htmlspecialchars($datosVisitado['username']); ?></h2>
    </div>
</div>

<div class="profile-subnav">
    <a href="listaperfil.php?id=<?php echo $id_perfil_visitado; ?>">General</a>
    <a href="animelistusuario.php?id=<?php echo $id_perfil_visitado; ?>">Anime List</a>
    <a href="mangalistusuario.php?id=<?php echo $id_perfil_visitado; ?>">Manga List</a>
    <a href="peliculatvlistusuario.php?id=<?php echo $id_perfil_visitado; ?>">TV List</a>
    <a href="amigos.php?id=<?php echo $id_perfil_visitado; ?>">Social</a>
    <a href="reviews_usuario.php?id=<?php echo $id_perfil_visitado; ?>" class="active">Reseñas</a>
</div>

<div class="profile-body-container">
    <div class="profile-left-col">
        <div class="content-card">
            <h3 class="card-title">Total Reseñas</h3>
            <p style="font-size: 2em; color: #ffdd1c; font-weight: bold;">
                <?php echo count($user_reviews); ?>
            </p>
        </div>
    </div>

    <div class="profile-right-col">
        <div class="content-section">
            <h3 class="section-title">Todas Las Reseñas</h3>
            
            <?php if (empty($user_reviews)): ?>
                <p class="empty-text">Este usuario aún no ha escrito reseñas.</p>
            <?php else: ?>
                <?php foreach ($user_reviews as $rev): 
                    $id_link = ($rev['type'] == 'anime') ? $rev['mal_id'] : $rev['tmdb_id'];
                    $enlace = "media.php?id=" . $id_link . "&type=" . $rev['type'];
                ?>
                    <div class="profile-review-card" style="display: flex; background: #151f2e; margin-bottom: 20px; border-radius: 8px; border: 1px solid #233044; padding: 15px; gap: 20px;">
                        <a href="<?php echo $enlace; ?>">
                            <img src="<?php echo htmlspecialchars($rev['portada']); ?>" style="width: 80px; height: 115px; object-fit: cover; border-radius: 4px; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
                        </a>
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; border-bottom: 1px solid #233044; padding-bottom: 5px;">
                                <a href="<?php echo $enlace; ?>" style="color: #ffdd1c; font-weight: bold; text-decoration: none; font-size: 1.1em;">
                                    <?php echo htmlspecialchars($rev['titulo']); ?>
                                </a>
                                <small style="color: #8ba0b2;">
                                    <?php echo date("d M, Y", strtotime($rev['created_at'])); ?>
                                </small>
                            </div>
                            <p style="color: #ced9e5; font-size: 0.95em; line-height: 1.6; margin: 0; white-space: pre-wrap;"><?php echo htmlspecialchars($rev['texto_resena']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>