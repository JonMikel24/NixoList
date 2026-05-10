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

$seccion = isset($_GET['seccion']) ? $_GET['seccion'] : 'inicio';

if ($seccion == 'inicio') {
    $topAnime = callAPI("https://api.jikan.moe/v4/top/anime?limit=10");
    $popularAnime = callAPI("https://api.jikan.moe/v4/top/anime?filter=bypopularity&limit=10");
    $upcomingAnime = callAPI("https://api.jikan.moe/v4/seasons/upcoming?limit=10");
    $recommendedAnime = callAPI("https://api.jikan.moe/v4/recommendations/anime"); 
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Anime</title>
<link rel="stylesheet" href="../../CSS/styles.css">

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
                $Foto = (!empty($_SESSION['Foto'])) ? $_SESSION['Foto']  : '../../Recursos/fotos_perfil/fotousuario.png';
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
                <a href="manga.php#recomendados">Recomendados</a>
                <a href="manga.php#populares">Más Populares</a>
                <a href="manga.php#top">Top Manga</a>
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
<?php if ($seccion == 'inicio') { ?>

<h2>Recomendados</h2>
<div class="carousel">

<?php
foreach(array_slice($recommendedAnime["data"],0,10) as $anime){

    $entry = $anime["entry"][0];

    $title = $entry["title"];
    $img = $entry["images"]["jpg"]["image_url"];
    $id = $entry["mal_id"]; // id de MyAnimeList
    $type = "anime"; // tipo fijo

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
foreach($popularAnime["data"] as $anime){

    $title = $anime["title"];
    $img = $anime["images"]["jpg"]["image_url"];
    $id = $anime["mal_id"]; // <--- importante
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

<h2>Próximos Lanzamientos</h2>
<div class="carousel">

<?php
foreach(array_slice($upcomingAnime["data"],0,10) as $anime){
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

<?php } elseif ($seccion == 'recomendados') {
    echo "<h2>Animes Recomendados</h2>";
    echo "<div class='grid-galeria'>";
    $paginaRecomendados = callAPI("https://api.jikan.moe/v4/recommendations/anime");
    
    foreach(array_slice($paginaRecomendados["data"],0,25) as $anime){
        $entry = $anime["entry"][0];
        $id = $entry["mal_id"];
        $img = $entry["images"]["jpg"]["image_url"];
        echo "<div class='card'><a href='media.php?id=$id&type=anime'><img src='$img'></a><p>{$entry['title']}</p></div>";
    }
    echo "</div>";

} elseif ($seccion == 'populares') {
    echo "<h2>Animes Más Populares</h2>";
    echo "<div class='grid-galeria'>";
    $paginaPopulares = callAPI("https://api.jikan.moe/v4/top/anime?filter=bypopularity&limit=25");
    
    foreach($paginaPopulares["data"] as $anime){
        $id = $anime["mal_id"];
        $img = $anime["images"]["jpg"]["image_url"];
        echo "<div class='card'><a href='media.php?id=$id&type=anime'><img src='$img'></a><p>{$anime['title']}</p></div>";
    }
    echo "</div>";

} elseif ($seccion == 'top') {
    echo "<h2>Top Anime de Todos los Tiempos</h2>";
    echo "<div class='grid-galeria'>";
    $paginaTop = callAPI("https://api.jikan.moe/v4/top/anime?limit=25");
    
    foreach($paginaTop["data"] as $anime){
        $id = $anime["mal_id"];
        $img = $anime["images"]["jpg"]["image_url"];
        echo "<div class='card'><a href='media.php?id=$id&type=anime'><img src='$img'></a><p>{$anime['title']}</p></div>";
    }
    echo "</div>";
}
?>

</div>

</body>
</html>