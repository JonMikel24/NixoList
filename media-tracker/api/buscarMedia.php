<?php

require_once "../config/conexion.php";
require_once "../services/tmdbService.php";
require_once "../services/jikanService.php";

$query = $_GET['query'] ?? '';

$results = [];

if(strlen($query) > 2){

    // 1 Buscar en DB
    $stmt = $conn->prepare("
        SELECT * FROM media
        WHERE titulo LIKE ?
        LIMIT 20
    ");

    $like = "%$query%";

    $stmt->bind_param("s",$like);
    $stmt->execute();

    $dbResults = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    if(count($dbResults) > 0){
        $results = $dbResults;
    }
    else{

        // 2 Buscar en TMDB
        $tmdb = searchTMDB($query);

        // 3 Buscar anime
        $anime = searchAnime($query);

        $results = [
            "tmdb"=>$tmdb,
            "anime"=>$anime
        ];
    }
}

echo json_encode($results);