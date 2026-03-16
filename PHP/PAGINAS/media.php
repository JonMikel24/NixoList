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
<body>

<nav class="navbar">
<h1>NixoList</h1>
<div class="nav-links">
<a href="anime.php">Anime</a>
<a href="peliculas.php">Películas</a>
<a href="series.php">Series</a>
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