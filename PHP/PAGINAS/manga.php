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
    // Aumentamos a 2 segundos para que la API de Jikan no nos bloquee (Error 429)
    $topManga = callAPI("https://api.jikan.moe/v4/top/manga?limit=10");
    sleep(2); 

    $popularManga = callAPI("https://api.jikan.moe/v4/top/manga?filter=bypopularity&limit=10");
    sleep(2); 


    $recommendedManga = callAPI("https://api.jikan.moe/v4/recommendations/manga"); 
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Manga - NixoList</title>
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
    // Obtenemos el nombre del archivo actual (ej: manga.php)
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


<div class="container">
<?php if ($seccion == 'inicio') { ?>

<h2>Mangas Recomendados</h2>
<div class="carousel">

<?php
if(isset($recommendedManga["data"])) {
    foreach(array_slice($recommendedManga["data"],0,10) as $manga){

        $entry = $manga["entry"][0];

        $title = $entry["title"];
        $img = $entry["images"]["jpg"]["image_url"];
        $id = $entry["mal_id"]; // id de MyAnimeList
        $type = "manga"; // tipo fijo

        echo "
        <div class='card'>
            <a href='media.php?id=$id&type=$type'>
                <img src='$img'>
            </a>
            <p>$title</p>
        </div>
        ";
    }
}
?>

</div>

<h2>Mangas Más Populares</h2>
<div class="carousel">

<?php
if(isset($popularManga["data"])) {
    foreach(array_slice($popularManga["data"],0,10) as $manga){

        $title = $manga["title"];
        $img = $manga["images"]["jpg"]["image_url"];
        $id = $manga["mal_id"]; 
        $type = "manga";

        echo "
        <div class='card'>
            <a href='media.php?id=$id&type=$type'>
                <img src='$img'>
            </a>
            <p>$title</p>
        </div>
        ";
    }
}
?>

</div>

<h2>Top Manga</h2>
<div class="carousel">

<?php
if(isset($topManga["data"])) {
    foreach(array_slice($topManga["data"],0,10) as $manga){
        $title = $manga["title"];
        $img = $manga["images"]["jpg"]["image_url"];
        $id = $manga["mal_id"];
        $type = "manga";

        echo "
        <div class='card'>
            <a href='media.php?id=$id&type=$type'>
                <img src='$img'>
            </a>
            <p>$title</p>
        </div>
        ";
    }
}
?>

</div>


<?php } elseif ($seccion == 'recomendados') {
    echo "<h2>Mangas Recomendados</h2>";
    echo "<div class='grid-galeria'>";
    $paginaRecomendados = callAPI("https://api.jikan.moe/v4/recommendations/manga");
    
    if(isset($paginaRecomendados["data"])) {
        foreach(array_slice($paginaRecomendados["data"],0,25) as $manga){
            $entry = $manga["entry"][0];
            $id = $entry["mal_id"];
            $img = $entry["images"]["jpg"]["image_url"];
            echo "<div class='card'><a href='media.php?id=$id&type=manga'><img src='$img'></a><p>{$entry['title']}</p></div>";
        }
    }
    echo "</div>";

} elseif ($seccion == 'populares') {
    echo "<h2>Mangas Más Populares</h2>";
    echo "<div class='grid-galeria'>";
    $paginaPopulares = callAPI("https://api.jikan.moe/v4/top/manga?filter=bypopularity&limit=10");
    
    if(isset($paginaPopulares["data"])) {
        foreach($paginaPopulares["data"] as $manga){
            $id = $manga["mal_id"];
            $img = $manga["images"]["jpg"]["image_url"];
            echo "<div class='card'><a href='media.php?id=$id&type=manga'><img src='$img'></a><p>{$manga['title']}</p></div>";
        }
    }
    echo "</div>";

} elseif ($seccion == 'top') {
    echo "<h2>Top Manga de Todos los Tiempos</h2>";
    echo "<div class='grid-galeria'>";
    $paginaTop = callAPI("https://api.jikan.moe/v4/top/manga?limit=10");
    
    if(isset($paginaTop["data"])) {
        foreach($paginaTop["data"] as $manga){
            $id = $manga["mal_id"];
            $img = $manga["images"]["jpg"]["image_url"];
            echo "<div class='card'><a href='media.php?id=$id&type=manga'><img src='$img'></a><p>{$manga['title']}</p></div>";
        }
    }
    echo "</div>";
}
?>

</div>

</body>
</html>