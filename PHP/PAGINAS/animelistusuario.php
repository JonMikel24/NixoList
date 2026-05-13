<?php
// ¡Magia! Con esta línea traemos todos los datos procesados del otro archivo
require_once '../FUNCIONALIDADES/logica_animelist.php';

?>
    <link rel="stylesheet" href="../../CSS/medialistusuario.css">




    <!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Anime</title>
<link rel="stylesheet" href="../../CSS/styles.css">
<link rel="icon" type="image/png" href="../../Recursos/icono/icononixo.png">

</head>
<header class="header-main">
    <div class="header-top">
        <div class="logo-container">
            <a href="index.php" class="enlace-logo">
                <h1 class="logo-texto">NixoList</h1>
            </a>
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
<body>
    <?php
    // Obtenemos el nombre del archivo actual (ej: anime.php)
    $pagina_actual = basename($_SERVER['PHP_SELF']);
    ?>

<nav class="navbar">
    <div class="nav-links">
        
        <a href="index.php" class="<?php echo ($pagina_actual == 'index.php') ? 'active' : ''; ?>">Inicio</a>

        <div class="menu-desplegable">
            <a href="anime.php" class="seccion-principal <?php echo ($pagina_actual == 'anime.php') ? 'active' : ''; ?>">Anime</a>
            <div class="sub-menu">
                <a href="anime.php">Inicio Anime</a>
                <a href="anime.php?seccion=recomendados">Recomendados</a>
                <a href="anime.php?seccion=populares">Más Populares</a>
                <a href="anime.php?seccion=top">Top Anime</a>
            </div>
        </div>

        <div class="menu-desplegable">
            <a href="manga.php" class="seccion-principal <?php echo ($pagina_actual == 'manga.php') ? 'active' : ''; ?>">Manga</a>
            <div class="sub-menu">
                <a href="manga.php">Inicio Manga</a>
                <a href="manga.php?seccion=recomendados">Recomendados</a>
                <a href="manga.php?seccion=populares">Más Populares</a>
                <a href="manga.php?seccion=top">Top Manga</a>
            </div>
        </div>

        <div class="menu-desplegable">
            <a href="peliculas.php" class="seccion-principal <?php echo ($pagina_actual == 'peliculas.php') ? 'active' : ''; ?>">Películas</a>
            <div class="sub-menu">
                <a href="peliculas.php">Inicio Películas</a>
                <a href="peliculas.php?seccion=recomendadas">Recomendadas</a>
                <a href="peliculas.php?seccion=populares">Más Populares</a>
                <a href="peliculas.php?seccion=top">Top Rated</a>
            </div>
        </div>

        <div class="menu-desplegable">
            <a href="series.php" class="seccion-principal <?php echo ($pagina_actual == 'series.php') ? 'active' : ''; ?>">Series</a>
            <div class="sub-menu">
                <a href="series.php">Inicio Series</a>
                <a href="series.php?seccion=trending">Trending</a>
                <a href="series.php?seccion=populares">Más Populares</a>
                <a href="series.php?seccion=top">Top Rated</a>
            </div>
        </div>

        <div class="menu-desplegable">
            <a href="juegos.php" class="seccion-principal <?php echo (in_array($pagina_actual, ['juegos.php', 'juegoOpeningsAnime.php', 'juegoPersonajesAnime.php', 'juegoWordleAnime.php', 'juegoAhorcadoAnime.php'])) ? 'active' : ''; ?>">Juegos</a>
            <div class="sub-menu">
                <a href="juegos.php">Inicio Juegos</a>
                <a href="juegoOpeningsAnime.php">Adivina el Opening</a>
                <a href="juegoPersonajesAnime.php">Adivina el Personaje</a>
                <a href="juegoWordleAnime.php">Wordle Anime</a>
                <a href="juegoAhorcadoAnime.php">Ahorcado Anime</a>
            </div>
        </div>
    </div>

    <div class="search-wrapper" style="position: relative;"> 
        <div class="search-container">
            <select class="search-select" id="search-type">
                <option value="all">Todos</option>
                <option value="anime">Anime</option>
                <option value="manga">Manga</option>
                <option value="movie">Películas</option>
                <option value="tv">Series</option>
            </select>
            <input type="text" id="search-input" placeholder="Buscar..." class="search-input" autocomplete="off">
            <button type="submit" class="search-button"><i>🔍</i></button>
        </div>
        <div id="search-results" class="search-results-dropdown"></div>
    </div>
</nav>

<div class="list-container">
    <?php
    $listaAgrupada = isset($listaAgrupada) ? $listaAgrupada : [];
    $nombresEstados = isset($nombresEstados) ? $nombresEstados : [];
    
    foreach ($listaAgrupada as $estado => $lista): ?>
        <?php if (count($lista) > 0): 
            // --- BLOQUE DE ORDENACIÓN ---
            // Ordenamos la lista actual por 'puntuacion' de mayor a menor
            usort($lista, function($a, $b) {
                return $b['puntuacion'] <=> $a['puntuacion'];
            });
        ?>
            <div class="status-section">
                <h2 class="status-title"><?php echo isset($nombresEstados[$estado]) ? $nombresEstados[$estado] : 'Reading'; ?></h2>                
                <table class="anime-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th class="center-text" style="width: 100px;">Score</th>
                            <th class="center-text" style="width: 120px;">Progress</th>
                            <th class="center-text" style="width: 100px;">Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lista as $item): 
                            // Corrección del ID para que use mal_id tanto en anime como en manga si es necesario
                            $tipoLower = strtolower($item['type']);
                            $id_para_link = ($tipoLower === 'anime' || $tipoLower === 'manga') ? $item['mal_id'] : $item['tmdb_id']; 
                        ?>
                            <tr>
                                <td>
                                    <div class="anime-title-col">
                                        <img src="<?php echo htmlspecialchars($item['portada']); ?>" alt="Cover" class="anime-cover">
                                        <a href="media.php?id=<?php echo urlencode($id_para_link); ?>&type=<?php echo urlencode($item['type']); ?>" class="anime-name">
                                            <?php echo htmlspecialchars($item['titulo']); ?>
                                        </a>
                                    </div>
                                </td>
                                <td class="center-text">
                                    <strong style="color: #ffcc00;">
                                        <?php echo ($item['puntuacion'] > 0) ? $item['puntuacion'] : '-'; ?>
                                    </strong>
                                </td>
                                <td class="center-text">
                                    <?php echo $item['episodios_vistos']; ?> / <?php echo ($item['episodios_totales'] > 0) ? $item['episodios_totales'] : '?'; ?>
                                </td>
                                <td class="center-text" style="text-transform: uppercase;">
                                    <?php echo htmlspecialchars($item['type']); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>