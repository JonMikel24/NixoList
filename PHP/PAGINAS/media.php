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

$id = $_GET['id'] ?? 0;
$type = $_GET['type'] ?? 'movie'; // movie / tv / anime

if($type == "anime"){
    $media = callAPI("https://api.jikan.moe/v4/anime/".$id);
    $title = $media["data"]["title"];
    $img = $media["data"]["images"]["jpg"]["image_url"];
    $desc = $media["data"]["synopsis"];
    $score = $media["data"]["score"];
    $episodes = $media["data"]["episodes"];
}else{
    $media = callAPI("https://api.themoviedb.org/3/".$type."/".$id."?api_key=".$tmdb_key);
    $title = $media["title"] ?? $media["name"];
    $img = "https://image.tmdb.org/t/p/w500".$media["poster_path"];
    $desc = $media["overview"];
    $score = $media["vote_average"];
    $episodes = $media["number_of_episodes"] ?? "-";
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo $title; ?></title>
<link rel="stylesheet" href="../../CSS/styles.css">
</head>
<header class="header-main">
    <div class="header-top">
        <div class="logo-container">
            <h1 class="logo-texto">NixoList</h1>
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

<div class="container media-page">
<div class="media-details">
<img src="<?php echo $img; ?>" class="media-poster">
<div class="media-info">
<h2><?php echo $title; ?></h2>
<p><strong>Score:</strong> <?php echo $score; ?></p>
<p><strong>Episodes / Duration:</strong> <?php echo $episodes; ?></p>
<p><?php echo $desc; ?></p>
<button>Añadir a favoritos</button>
</div>
</div>
</div>

</body>

</html>