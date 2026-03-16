<?php
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
<body>

<nav class="navbar">
<h1>NixoList</h1>
<div class="nav-links">
<a href="anime.php">Anime</a>
<a href="movies.php">Películas</a>
<a href="series.php">Series</a>
</div>
</nav>

<div class="container">

<input type="text" placeholder="Buscar serie..." class="search">

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