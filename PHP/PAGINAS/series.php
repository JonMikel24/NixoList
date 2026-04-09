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

$tmdb_key="0537b412710df9a2b7790cada44e494e";

// Endpoints TMDB
$popular = callAPI("https://api.themoviedb.org/3/tv/popular?api_key=".$tmdb_key);
$topRated = callAPI("https://api.themoviedb.org/3/tv/top_rated?api_key=".$tmdb_key);
$onAir = callAPI("https://api.themoviedb.org/3/tv/on_the_air?api_key=".$tmdb_key);
$trending = callAPI("https://api.themoviedb.org/3/trending/tv/week?api_key=".$tmdb_key);

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Series</title>
<link rel="stylesheet" href="../../CSS/styles.css">
</head>
<header class="header-main">
    <div class="header-top">
        <div class="logo-container">
            <a href="index.php" class="enlace-logo">
                <h1 class="logo-texto">NixoList</h1>
            </a>
        </div>

        <div class="PerfilContenedor" onclick="abrirPerfil()" style="cursor:pointer;">
            <?php
            if (isset($_SESSION['Usuario'])) {
                $Foto = (!empty($_SESSION['Foto'])) ? $_SESSION['Foto'] : '/Recursos/fotousuario.png';
                echo '
                <div class="perfil-horiz">
                    <div class="perfil-info">
                        <p class="perfil-nombre nombre-mio">' . htmlspecialchars($_SESSION['Usuario']) . ' <span class="flecha">▼</span></p>
                    </div>
                    <img src="' . htmlspecialchars($Foto) . '" class="profile-pic foto-mia" id="perfilImagen">
                </div>
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
                <a href="anime.php#recomendados">Recomendados</a>
                <a href="anime.php#populares">Más Populares</a>
                <a href="anime.php#top">Top Anime</a>
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

    </div>

    <div class="search-container">
        <select class="search-select">
            <option value="all">All</option>
            <option value="anime">Anime</option>
            <option value="manga">Manga</option>
        </select>
        <input type="text" placeholder="Search Anime, Manga, and more..." class="search-input">
        <button type="submit" class="search-button">
            <i>🔍</i> 
        </button>
    </div>
</nav>


<div class="container">


<h2>Trending</h2>
<div class="carousel">
<?php
foreach(array_slice($trending["results"],0,10) as $s){
    $title = $s["name"];
    $img = "https://image.tmdb.org/t/p/w500".$s["poster_path"];
    $id = $s["id"];
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

<h2>Más Populares</h2>
<div class="carousel">
<?php
foreach(array_slice($popular["results"],0,10) as $s){
    $title = $s["name"];
    $img = "https://image.tmdb.org/t/p/w500".$s["poster_path"];
    $id = $s["id"];
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

<h2>Top Rated</h2>
<div class="carousel">
<?php
foreach(array_slice($topRated["results"],0,10) as $s){
    $title = $s["name"];
    $img = "https://image.tmdb.org/t/p/w500".$s["poster_path"];
    $id = $s["id"];
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

</html>