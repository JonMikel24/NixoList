<?php

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
</head>
<body>

<nav class="navbar">
    <h1>NixoList</h1>
    <div class="nav-links">
        <a href="anime.php">Anime</a>
        <a href="peliculas.php">Películas</a>
        <a href="series.php">Series</a>
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

</html>