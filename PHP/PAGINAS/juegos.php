<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>NixoList - Juegos</title>
<link rel="stylesheet" href="../../CSS/styles.css">
<link rel="stylesheet" href="../../CSS/juegos.css">
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

<?php $pagina_actual = basename($_SERVER['PHP_SELF']); ?>

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

<div class="container">
    <h2 style="text-align: center; margin-top: 20px;">Zona de Juegos de NixoList</h2>
    <p style="text-align: center; color: #ccc;">Pon a prueba tus conocimientos otaku con nuestros minijuegos</p>

    <div class="juegos-grid">
        <div class="juego-card">
            <h3>Adivina el Opening</h3>
            <p>Escucha un fragmento de audio y adivina a qué anime pertenece el opening</p>
            <a href="juegoOpeningsAnime.php" class="btn-jugar">Jugar Ahora</a>
        </div>

        <div class="juego-card">
            <h3>Adivina el Personaje</h3>
            <p>Adivina el personaje de anime por su imagen</p>
            <a href="juegoPersonajesAnime.php" class="btn-jugar">Jugar Ahora</a>
        </div>

        <div class="juego-card">
            <h3>Wordle Anime</h3>
            <p>Descifra el personaje de anime de 6 letras en 6 intentos</p>
            <a href="juegoWordleAnime.php" class="btn-jugar">Jugar Ahora</a>
        </div>

        <div class="juego-card">
            <h3>Ahorcado Anime</h3>
            <p>Descubre el anime letra por letra antes de perder tus vidas</p>
            <a href="juegoAhorcadoAnime.php" class="btn-jugar">Jugar Ahora</a>
        </div>
    </div>
</div>
<script>
    document.getElementById('search-input').addEventListener('input', function() {
    let query = this.value;
    let type = document.getElementById('search-type').value;
    let resultsContainer = document.getElementById('search-results');

    if (query.length >= 3) {
        fetch(`buscar_sugerencias.php?q=${query}&type=${type}`)
            .then(response => response.text())
            .then(data => {
                resultsContainer.innerHTML = data;
                resultsContainer.style.display = 'block'; 
            });
    } else {
        resultsContainer.style.display = 'none'; 
    }
});
</script>
</body>
</html>