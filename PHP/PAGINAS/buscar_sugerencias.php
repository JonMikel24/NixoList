<?php
$query = urlencode($_GET['q']);
$type = $_GET['type'];
$tmdb_key = "0537b412710df9a2b7790cada44e494e";

$results = [];

function get_api_data($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return ($httpCode === 200) ? json_decode($response, true) : null;
}

if ($type == 'all' || $type == 'anime' || $type == 'manga') {
    $jikan_type = ($type == 'all') ? 'anime' : $type;
    $data = get_api_data("https://api.jikan.moe/v4/$jikan_type?q=$query&limit=5");
    
    if (!empty($data['data'])) {
        foreach ($data['data'] as $item) {
            $results[] = [
                'title' => $item['title'],
                'img' => $item['images']['jpg']['image_url'],
                'url' => "media.php?id=" . $item['mal_id'] . "&type=$jikan_type"
            ];
        }
    }
}


if ($type == 'all' || $type == 'movie' || $type == 'tv') {
    $tmdb_endpoint = ($type == 'all') ? 'multi' : $type;
    $data = get_api_data("https://api.themoviedb.org/3/search/$tmdb_endpoint?api_key=$tmdb_key&query=$query");
    
    if (!empty($data['results'])) {
        foreach (array_slice($data['results'], 0, 5) as $item) {
            $title = isset($item['title']) ? $item['title'] : ($item['name'] ?? 'Sin título');
            $media_type = $item['media_type'] ?? $type;
            
            if ($media_type == 'person' || empty($item['poster_path'])) continue;
            
            $results[] = [
                'title' => $title,
                'img' => "https://image.tmdb.org/t/p/w92" . $item['poster_path'],
                'url' => "media.php?id=" . $item['id'] . "&type=$media_type"
            ];
        }
    }
}


if (empty($results)) {
    echo "<div style='padding:20px; text-align:center; color:#8ba0b2; font-size:14px;'>No se encontraron resultados para \"".htmlspecialchars($_GET['q'])."\"</div>";
} else {
    foreach ($results as $res) {        
        echo "
        <a href='{$res['url']}' class='result-item'>
            <div class='result-poster-wrapper'>
                <img src='{$res['img']}' alt='poster' class='result-poster'>
            </div>
            <div class='result-info'>
                <span class='result-title'>".htmlspecialchars($res['title'])."</span>
                <span class='result-meta'>".ucfirst($type === 'all' ? (strpos($res['url'], 'type=anime') !== false ? 'Anime' : 'Media') : $type)."</span>
            </div>
        </a>";
    }
}