<?php

function callAPI($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response,true);
}

$topAnime = callAPI("https://api.jikan.moe/v4/top/anime?limit=10");
$popularAnime = callAPI("https://api.jikan.moe/v4/top/anime?filter=bypopularity&limit=10");
$upcomingAnime = callAPI("https://api.jikan.moe/v4/seasons/upcoming?limit=10");
$recommendedAnime = callAPI("https://api.jikan.moe/v4/recommendations/anime");

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Anime</title>
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

<input type="text" placeholder="Buscar anime..." class="search">

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

</div>

</body>
</html>