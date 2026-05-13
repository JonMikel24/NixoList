<?php
session_start();

function callAPI($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response,true);
}

$tmdb_key = "0537b412710df9a2b7790cada44e494e";

$topAnime = callAPI("https://api.jikan.moe/v4/top/anime?limit=10");
$topMovies = callAPI("https://api.themoviedb.org/3/trending/movie/week?api_key=".$tmdb_key);
$topSeries = callAPI("https://api.themoviedb.org/3/trending/tv/week?api_key=".$tmdb_key);

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>NixoList</title>
<link rel="stylesheet" href="../../CSS/styles.css">
<link rel="icon" type="image/png" href="../../Recursos/icono/icononixo.png">

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

<div class="container">

<h2>Top Anime</h2>
<div class="carousel">
<?php
foreach($topAnime["data"] as $anime){
    $title = $anime["title"];
    $img = $anime["images"]["jpg"]["image_url"];
    $id = $anime["mal_id"];
    $type = "anime";

    echo "
    <div class='card'>
        <a href='media.php?id=$id&type=$type'>
            <img src='$img'>
        </a>
        <p>$title</p>
    </div>
    ";
}
?>
</div>

<h2>Top Películas</h2>
<div class="carousel">
<?php
foreach(array_slice($topMovies["results"],0,10) as $movie){
    $title = $movie["title"];
    $img = "https://image.tmdb.org/t/p/w500".$movie["poster_path"];
    $id = $movie["id"];
    $type = "movie";

    echo "
    <div class='card'>
        <a href='media.php?id=$id&type=$type'>
            <img src='$img'>
        </a>
        <p>$title</p>
    </div>
    ";
}
?>
</div>

<h2>Top Series</h2>
<div class="carousel">
<?php
foreach(array_slice($topSeries["results"],0,10) as $serie){
    $title = $serie["name"];
    $img = "https://image.tmdb.org/t/p/w500".$serie["poster_path"];
    $id = $serie["id"];
    $type = "tv";

    echo "
    <div class='card'>
        <a href='media.php?id=$id&type=$type'>
            <img src='$img'>
        </a>
        <p>$title</p>
    </div>
    ";
}
?>
</div>

</div>
</body>
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


document.addEventListener('click', function(e) {
    if (!document.querySelector('.search-wrapper').contains(e.target)) {
        document.getElementById('search-results').style.display = 'none';
    }
});
</script>

</html>