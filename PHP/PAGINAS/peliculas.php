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

$popular = callAPI("https://api.themoviedb.org/3/movie/popular?api_key=".$tmdb_key);
$topRated = callAPI("https://api.themoviedb.org/3/movie/top_rated?api_key=".$tmdb_key);
$upcoming = callAPI("https://api.themoviedb.org/3/movie/upcoming?api_key=".$tmdb_key);
$trending = callAPI("https://api.themoviedb.org/3/trending/movie/week?api_key=".$tmdb_key);

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Películas</title>
<link rel="stylesheet" href="../../CSS/styles.css">
</head>
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
<body>
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


<h2>Recomendadas</h2>
<div class="carousel">
<?php
foreach(array_slice($trending["results"],0,10) as $movie){
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

<h2>Más Populares</h2>
<div class="carousel">
<?php
foreach(array_slice($popular["results"],0,10) as $movie){
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

<h2>Top Rated</h2>
<div class="carousel">
<?php
foreach(array_slice($topRated["results"],0,10) as $movie){
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

<h2>Próximos Lanzamientos</h2>
<div class="carousel">
<?php
foreach(array_slice($upcoming["results"],0,10) as $movie){
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

</div>
</body>

</html>